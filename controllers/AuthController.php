<?php
/**
 * EnPharChem - Authentication Controller
 */

class AuthController extends BaseController {

    public function login() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
        }

        $error = '';
        if ($this->isPost()) {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = 'Please enter both username and password.';
            } else {
                $user = $this->db->fetch(
                    "SELECT * FROM users WHERE (username = ? OR email = ?) AND is_active = 1",
                    [$username, $username]
                );

                if ($user && password_verify($password, $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];

                    $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);

                    $this->redirect('dashboard');
                } else {
                    $error = 'Invalid username or password.';
                }
            }
        }

        $this->viewWithoutLayout('auth/login', ['error' => $error]);
    }

    public function register() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
        }

        $error = '';
        $success = '';

        if ($this->isPost()) {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $company = trim($_POST['company'] ?? '');

            if (empty($username) || empty($email) || empty($password)) {
                $error = 'Please fill in all required fields.';
            } elseif ($password !== $confirmPassword) {
                $error = 'Passwords do not match.';
            } elseif (strlen($password) < 8) {
                $error = 'Password must be at least 8 characters.';
            } else {
                $existing = $this->db->fetch(
                    "SELECT id FROM users WHERE username = ? OR email = ?",
                    [$username, $email]
                );

                if ($existing) {
                    $error = 'Username or email already exists.';
                } else {
                    $this->db->insert('users', [
                        'username' => $username,
                        'email' => $email,
                        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'company' => $company,
                        'role' => 'engineer',
                        'license_type' => 'trial',
                    ]);
                    $success = 'Account created successfully. Please log in.';
                }
            }
        }

        $this->viewWithoutLayout('auth/register', ['error' => $error, 'success' => $success]);
    }

    public function logout() {
        session_destroy();
        header('Location: ' . APP_URL . '/login');
        exit;
    }
}
