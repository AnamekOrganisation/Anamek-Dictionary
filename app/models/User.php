<?php

/**
 * User Model
 * Handles user authentication, registration, and profile management
 */
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Register a new user
     * @param array $data User data (username, email, password, full_name)
     * @return array|false User data on success, false on failure
     */
    public function register($data) {
        try {
            // Validate required fields
            if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
                throw new Exception("Username, email and password are required");
            }

            // Validate email format
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }

            // Validate password strength (min 12 characters + complexity)
            if (strlen($data['password']) < 12) {
                throw new Exception("Password must be at least 12 characters");
            }

            // SECURITY: Require password complexity
            if (!$this->isPasswordStrong($data['password'])) {
                throw new Exception("Password must contain uppercase, lowercase, number, and special character (@$!%*?&)");
            }

            // Check if username or email already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$data['username'], $data['email']]);
            if ($stmt->fetch()) {
                throw new Exception("Username or email already exists");
            }

            // Hash password
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

            // Generate email verification token
            $verificationToken = bin2hex(random_bytes(32));

            // Insert user
            $sql = "INSERT INTO users (username, email, password_hash, full_name, verification_token, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $data['username'],
                $data['email'],
                $passwordHash,
                $data['full_name'] ?? '',
                $verificationToken
            ]);

            $userId = $this->pdo->lastInsertId();

            // Return user data (excluding password)
            return [
                'id' => $userId,
                'username' => $data['username'],
                'email' => $data['email'],
                'full_name' => $data['full_name'] ?? '',
                'verification_token' => $verificationToken
            ];

        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Login user
     * @param string $emailOrUsername Email or username
     * @param string $password Password
     * @return array|false User data on success, false on failure
     */
    public function login($emailOrUsername, $password) {
        try {
            // Find user by email or username
            $sql = "SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$emailOrUsername, $emailOrUsername]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return false;
            }

            // Check if account is active
            if (!$user['is_active']) {
                throw new Exception("Account is deactivated");
            }

            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                return false;
            }

            // Update last login
            $updateStmt = $this->pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);

            // Remove password hash from return data
            unset($user['password_hash']);
            unset($user['verification_token']);
            unset($user['reset_token']);
            unset($user['reset_token_expires']);

            return $user;

        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find user by ID
     * @param int $id User ID
     * @return array|false User data or false
     */
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            unset($user['password_hash']);
            unset($user['verification_token']);
            unset($user['reset_token']);
        }

        return $user;
    }

    /**
     * Find user by email
     * @param string $email Email address
     * @return array|false User data or false
     */
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            unset($user['password_hash']);
        }

        return $user;
    }

    /**
     * Verify email with token
     * @param string $token Verification token
     * @return bool Success status
     */
    public function verifyEmail($token) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE verification_token = ?");
            $stmt->execute([$token]);
            $user = $stmt->fetch();

            if (!$user) {
                return false;
            }

            // Update user as verified
            $updateStmt = $this->pdo->prepare(
                "UPDATE users SET email_verified = 1, verification_token = NULL WHERE id = ?"
            );
            $updateStmt->execute([$user['id']]);

            return true;

        } catch (Exception $e) {
            error_log("Email verification error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Request password reset
     * @param string $email User email
     * @return string|false Reset token on success, false on failure
     */
    public function requestPasswordReset($email) {
        try {
            $user = $this->findByEmail($email);
            if (!$user) {
                return false;
            }

            // Generate reset token
            $resetToken = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Update user with reset token
            $stmt = $this->pdo->prepare(
                "UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?"
            );
            $stmt->execute([$resetToken, $expires, $email]);

            return $resetToken;

        } catch (Exception $e) {
            error_log("Password reset request error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reset password with token
     * @param string $token Reset token
     * @param string $newPassword New password
     * @return bool Success status
     */
    public function resetPassword($token, $newPassword) {
        try {
            // SECURITY: Validate password strength (min 12 characters + complexity)
            if (strlen($newPassword) < 12) {
                throw new Exception("Password must be at least 12 characters");
            }

            // SECURITY: Require password complexity
            if (!$this->isPasswordStrong($newPassword)) {
                throw new Exception("Password must contain uppercase, lowercase, number, and special character (@$!%*?&)");
            }

            // Find user with valid token (checks expiration)
            $stmt = $this->pdo->prepare(
                "SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()"
            );
            $stmt->execute([$token]);
            $user = $stmt->fetch();

            if (!$user) {
                return false;
            }

            // Hash new password
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update password and clear reset token
            $updateStmt = $this->pdo->prepare(
                "UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?"
            );
            $updateStmt->execute([$passwordHash, $user['id']]);

            return true;

        } catch (Exception $e) {
            error_log("Password reset error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user profile
     * @param int $userId User ID
     * @param array $data Profile data
     * @return bool Success status
     */
    public function updateProfile($userId, $data) {
        try {
            $allowedFields = ['full_name', 'bio', 'avatar_url'];
            $updates = [];
            $values = [];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = ?";
                    $values[] = $data[$field];
                }
            }

            if (empty($updates)) {
                return true;
            }

            $values[] = $userId;
            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);

            return true;

        } catch (Exception $e) {
            error_log("Profile update error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Change password
     * @param int $userId User ID
     * @param string $currentPassword Current password
     * @param string $newPassword New password
     * @return bool Success status
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        try {
            // Get user with password hash
            $stmt = $this->pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return false;
            }

            // Verify current password
            if (!password_verify($currentPassword, $user['password_hash'])) {
                throw new Exception("Current password is incorrect");
            }

            // Validate new password
            if (strlen($newPassword) < 8) {
                throw new Exception("Password must be at least 8 characters");
            }

            // Hash and update password
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $this->pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $updateStmt->execute([$passwordHash, $userId]);

            return true;

        } catch (Exception $e) {
            error_log("Password change error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user statistics
     * @param int $userId User ID
     * @return array Statistics
     */
    public function getStats($userId) {
        $stats = [
            'contribution_points' => 0,
            'total_contributions' => 0,
            'approved_contributions' => 0,
            'quizzes_taken' => 0,
            'avg_quiz_score' => 0
        ];

        try {
            // Get basic stats from user record
            $stmt = $this->pdo->prepare("SELECT contribution_points FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $stats['contribution_points'] = $user['contribution_points'];
            }

            // Get contribution stats
            $stmt = $this->pdo->prepare(
                "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved
                FROM user_contributions 
                WHERE user_id = ?"
            );
            $stmt->execute([$userId]);
            $contribStats = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($contribStats) {
                $stats['total_contributions'] = $contribStats['total'];
                $stats['approved_contributions'] = $contribStats['approved'];
            }

            // Get quiz stats
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) as total, AVG(percentage) as avg_score 
                FROM user_quiz_results 
                WHERE user_id = ?"
            );
            $stmt->execute([$userId]);
            $quizStats = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($quizStats) {
                $stats['quizzes_taken'] = $quizStats['total'];
                $stats['avg_quiz_score'] = round($quizStats['avg_score'], 1);
            }

        } catch (Exception $e) {
            error_log("Stats error: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Create user session
     * @param int $userId User ID
     * @param string $ipAddress IP address
     * @param string $userAgent User agent
     * @return string Session token
     */
    public function createSession($userId, $ipAddress, $userAgent) {
        $sessionToken = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

        $stmt = $this->pdo->prepare(
            "INSERT INTO user_sessions (user_id, session_token, ip_address, user_agent, expires_at) 
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $sessionToken, $ipAddress, $userAgent, $expiresAt]);

        return $sessionToken;
    }

    /**
     * Validate session token
     * @param string $sessionToken Session token
     * @return int|false User ID on success, false on failure
     */
    public function validateSession($sessionToken) {
        $stmt = $this->pdo->prepare(
            "SELECT user_id FROM user_sessions 
             WHERE session_token = ? AND expires_at > NOW()"
        );
        $stmt->execute([$sessionToken]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);

        return $session ? $session['user_id'] : false;
    }

    /**
     * Destroy session
     * @param string $sessionToken Session token
     * @return bool Success status
     */
    public function destroySession($sessionToken) {
        $stmt = $this->pdo->prepare("DELETE FROM user_sessions WHERE session_token = ?");
        return $stmt->execute([$sessionToken]);
    }

    /**
     * SECURITY: Validate password strength
     * Requirements: 12+ chars, uppercase, lowercase, digit, special char
     * @param string $password Password to validate
     * @return bool True if password meets requirements
     */
    private function isPasswordStrong($password) {
        $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}$/';
        return preg_match($pattern, $password) === 1;
    }
}
