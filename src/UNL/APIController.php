<?php

class UNL_APIController
{
    public function __construct($options = [])
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");

        $data = [];

        // If URL matches then call function
        if (strpos($_SERVER['REQUEST_URI'], '/api/v1/emailToUID') === 0) {
            $data = $this->getUIDFromEmail($options);
        }

        // Output the data
        echo json_encode($data);
    }

    /**
     * Converts the user's email to a UID
     *
     * @param array $options These are the options made in www/index.php
     * @return array Returns the UID in message.data or returns the error
     */
    public function getUIDFromEmail(array $options): array
    {
        // Validate we have the email param
        if (!isset($_GET['email']) || empty($_GET['email'])) {
            http_response_code(400);
            return [
                'status' => 400,
                'message' => 'Missing Email Param'
            ];
        }

        // Validate the email is an email
        if (filter_var($_GET['email'], FILTER_VALIDATE_EMAIL) === false) {
            http_response_code(400);
            return [
                'status' => 400,
                'message' => 'Email Param Is Not An Email'
            ];
        }

        // Try to get the user
        try {
            new UNL_Peoplefinder($options);
            $user_record = new UNL_Peoplefinder_Record(array('email' => $_GET['email']));
        } catch (Exception $e) {
            if ($e->getMessage() === 'Cannot find that Email.') {
                http_response_code(404);
                return [
                    'status' => 404,
                    'message' => 'Invalid Email'
                ];
            } else {
                http_response_code(500);
                return [
                    'status' => 500,
                    'message' => 'Something went wrong',
                    'error' => $e->getMessage()
                ];
            }
        }

        // If we get here, we made it
        return [
            'status' => 200,
            'message' => [
                'data' => $user_record->uid
            ]
        ];
    }
}
