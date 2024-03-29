<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

session_start(); // Start the session to store OTP

// Function to generate OTP
function generateOTP($length = 6) {
    $characters = '0123456789';
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $otp;
}

if(isset($_POST['send_otp'])) {
    // Fetch email address
    $to_email = $_POST['email'];

    // Generate OTP
    $otp = generateOTP();

    // Store OTP in session
    $_SESSION['otp'] = $otp;
    $_SESSION['email'] = $to_email;

    try {
        // PHPMailer configuration
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email'; // Update with your Gmail address
       //Go to google to your Google account and generate app password
        $mail->Password = 'your_password'; // Update with your Gmail password
        $mail->SMTPSecure = 'tls'; // Corrected setting
        $mail->Port = 587;

        // Email content
        $mail->setFrom('eugenevanlinsangan1204@gmail.com', 'Your Name'); // Update with your Gmail address
        $mail->addAddress($to_email);
        $mail->Subject = 'Your One-Time Password (OTP)';
        $mail->Body = 'Your OTP is: ' . $otp;

        // Send email
        $mail->send();
        echo "OTP has been sent to $to_email";
        
    } catch (Exception $e) {
        echo "Failed to send OTP. Please try again. Error: {$mail->ErrorInfo}";
    }
} elseif (isset($_POST['verify_otp'])) {
    // Fetch OTP entered by the user
    $user_otp = implode('', $_POST['otp']); // Concatenate OTP digits

    // Verify OTP
    if(isset($_SESSION['otp']) && $_SESSION['otp'] == $user_otp) {
        echo "OTP verified successfully!";
        // Clear session variables
        unset($_SESSION['otp']);
        unset($_SESSION['email']);
    } else {
        echo "Invalid OTP. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
        .container {
            position: relative; /* Added to position the animation */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input[type="email"] {
            margin: 5px;
            padding: 10px;
            width: 300px;
            box-sizing: border-box;
            border-radius: 10px;
        }

        input[type="number"] {
            margin: 5px;
            padding: 10px;
            width: 50px;
            box-sizing: border-box;
            border-radius: 10px;
        }

        input[type="submit"] {
            margin: 5px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        h2 {
            margin: 10px 0;
            width: 100%;
            text-align: center;
        }

        /* Checkmark animation */
        .checkmark {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            animation: fadeIn 0.5s forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="" method="post">
            <h2>OTP Verification</h2>
            <input type="email" name="email" placeholder="Enter your email" required value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>">
            <input type="submit" value="Send OTP" name="send_otp">
            <h2>Verify OTP</h2>
            <input type="number" name="otp[]" maxlength="1">
            <input type="number" name="otp[]" maxlength="1">
            <input type="number" name="otp[]" maxlength="1">
            <input type="number" name="otp[]" maxlength="1">
            <input type="number" name="otp[]" maxlength="1">
            <input type="number" name="otp[]" maxlength="1">
            <input type="submit" value="Verify OTP" name="verify_otp">    

            <?php if (isset($_POST['verify_otp']) && isset($_SESSION['otp']) && $_SESSION['otp'] == implode('', $_POST['otp'])): ?>
                <!-- Checkmark animation -->
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>


