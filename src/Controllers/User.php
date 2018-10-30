<?php

namespace GoPokedex\Controllers;

use Http\Request;
use Http\Response;
use GoPokedex\System\Mailer;
use GoPokedex\System\Template;
use Delight\Cookie\Session;
use Hashids\Hashids;

class User
{
    private $request;
    private $response;
    private $template;
    private $database;
    private $auth;

    public function __construct(Request $request, Response $response, Template $template, $database, $auth)
    {
        $this->request = $request;
        $this->response = $response;
        $this->template = $template;
        $this->database = $database;
        $this->auth = $auth;
    }

    public function doLogIn()
    {
        try {
            $this->auth->login(
                $this->request->getParameter('email'),
                $this->request->getParameter('password'),
                (int) (60 * 60 * 24 * 365.25)
            ); // user logged in
        } catch (\Delight\Auth\InvalidEmailException $e) {
            Session::set('action', 'login_error');
            Session::set('error', 'The email provided is not valid.');
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            Session::set('action', 'login_error');
            Session::set('error', 'The password provided is not valid.');
        } catch (\Delight\Auth\EmailNotVerifiedException $e) {
            Session::set('action', 'verification_error');
            Session::set('error', 'This account has not been verified.');
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            Session::set('action', 'login_error');
            Session::set('error', 'Too many requests.');
        }

        $this->response->redirect('/');
    }

    public function doCodeLogIn()
    {
        try {
            $this->auth->login(
                $this->request->getParameter('code').'@email.com',
                hash('md2', CONFIG['url']),
                (int) (60 * 60 * 24 * 365.25)
            ); // user logged in
        } catch (\Delight\Auth\InvalidEmailException $e) {
            Session::set('action', 'code_error');
            Session::set('error', 'invalid_code');
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            Session::set('action', 'code_error');
            Session::set('error', 'The password provided is not valid.');
        } catch (\Delight\Auth\EmailNotVerifiedException $e) {
            Session::set('action', 'code_error');
            Session::set('error', 'This account has not been verified.');
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            Session::set('action', 'code_error');
            Session::set('error', 'Too many requests.');
        }

        $this->response->redirect('/');
    }

    public function doLogOut()
    {
        $this->auth->logOut();
        $this->response->redirect('/');
    }

    public function doRegister()
    {
        $email = $this->request->getParameter('email');
        $password = $this->request->getParameter('password');
        $username = ($this->request->getParameter('username'))?$this->request->getParameter('username'):$email;

        try {
            $userId = $this->auth->register(
                $email,
                $password,
                $username,
                function ($selector, $token) use ($email, $password, $username) {
                    $html = $this->template->render('email_register', [
                        'registration_url' => CONFIG['url'].'/verify?code='.$selector.'&token='.$token,
                        'username' => $username
                    ], 'email');

                    $mail = new Mailer;
                    $mail->send([
                        'to' => $email,
                        'subject' => 'Welcome to GoPokedex ' . $username,
                        'html' => $html
                    ]);
                }
            );
            Session::set('action', 'registered');
            // we have signed up a new user
        } catch (\Delight\Auth\InvalidEmailException $e) {
            Session::set('action', 'registration_error');
            Session::set('error', 'The email provided is not valid.');
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            Session::set('action', 'registration_error');
            Session::set('error', 'The password provided is not valid.');
            var_dump('Invalid password: '.$password);
        } catch (\Delight\Auth\UserAlreadyExistsException $e) {
            Session::set('action', 'registration_error');
            Session::set('error', 'The email provided already exists.');
            var_dump('Email already exists: '.$email);
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            Session::set('action', 'registration_error');
            Session::set('error', 'Too many requests.');
        }

        $this->response->redirect('/');
    }

    public function doCodeRegister()
    {
        $email = rand() . '@email.com';
        $password = hash('md2', CONFIG['url']);
        $username = ($this->request->getParameter('code-username'))?
            $this->request->getParameter('code-username'):'Trainer';

        try {
            $userId = $this->auth->register(
                $email,
                $password,
                $username,
                function ($selector, $token) {
                    // no email to send for the unique code registration
                }
            );
            Session::set('action', 'registered');
            // we have signed up a new user
            $hashids = new Hashids('', 10, 'abcdefghijklmnopqrstvwxyz0123456789');
            $updated = false;
            while (!$updated) {
                $code = $hashids->encode(rand());
                $rows = $this->database->select('SELECT id FROM users WHERE email = ?', [$code]);
                if (empty($rows)) {
                    $this->database->update(
                        'users',
                        [
                            'email' => $code.'@email.com',
                            'verified' => 1
                        ],
                        [
                            'id' => $userId
                        ]
                    );
                    $updated = true;
                }
            }

            $this->auth->login(
                $code.'@email.com',
                hash('md2', CONFIG['url']),
                (int) (60 * 60 * 24 * 365.25)
            );

            Session::set('code', $code);
        } catch (\Delight\Auth\InvalidEmailException $e) {
            Session::set('action', 'registration_error');
            Session::set('error', 'The email provided is not valid.');
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            Session::set('action', 'registration_error');
            Session::set('error', 'The password provided is not valid.');
            var_dump('Invalid password: '.$password);
        } catch (\Delight\Auth\UserAlreadyExistsException $e) {
            $this->doCodeRegister();
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            Session::set('action', 'registration_error');
            Session::set('error', 'Too many requests.');
        }

        $this->response->redirect('/');
    }

    public function doVerify()
    {
        try {
            $this->auth->confirmEmail(
                $this->request->getParameter('code'),
                $this->request->getParameter('token')
            );

            \Delight\Cookie\Session::set('action', 'verified');

            // we have verified the user.
        } catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            Session::set('action', 'verification_error');
            Session::set('error', 'invalid_pair');
        } catch (\Delight\Auth\TokenExpiredException $e) {
            Session::set('action', 'verification_error');
            Session::set('error', 'token_expired');
        } catch (\Delight\Auth\UserAlreadyExistsException $e) {
            Session::set('action', 'verification_error');
            Session::set('error', 'The email provided already exists.');
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            Session::set('action', 'verification_error');
            Session::set('error', 'Too many requests.');
        }

        $this->response->redirect('/');
    }

    public function doReconfirm()
    {
        $email = $this->request->getParameter('email');

        try {
            $this->auth->resendConfirmationForEmail(
                $email,
                function ($selector, $token) use ($email) {
                    $html = $this->template->render('email_reconfirm', [
                        'registration_url' => CONFIG['url'].'/verify?code='.$selector.'&token='.$token
                    ], 'email');

                    $mail = new Mailer;
                    $mail->send([
                        'to' => $email,
                        'subject' => 'GoPokedex: Activate your account',
                        'html' => $html
                    ]);
                }
            );
            Session::set('action', 'reconfirmed');
            // we have resent the activation email
        } catch (\Delight\Auth\ConfirmationRequestNotFound $e) {
            Session::set('action', 'registration_error');
            Session::set('error', 'We could not find an account with that email. Please, register a new account.');
        } catch (\Delight\Auth\TooManyRequestsException $e) {
            Session::set('action', 'verification_error');
            Session::set('error', 'Too many requests.');
        }

        $this->response->redirect('/');
    }
}
