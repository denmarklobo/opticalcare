<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Accepted</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1 {
            color: #28a745;
        }
        p {
            line-height: 1.5;
        }
        .footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #888;
        }
        .button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #28a745;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Reservation is Accepted!</h1>
        <p>Dear <?php echo e($reservation->patient->full_name); ?>,</p>
        <p>Your reservation with ID <strong>#<?php echo e($reservation->id); ?></strong> has been accepted and is ready for pick up.</p>
        <p><strong>Product:</strong> <?php echo e($reservation->product->product_name); ?></p>
        <p><strong>Quantity:</strong> <?php echo e($reservation->quantity); ?></p>
        
        <a href="https://opticare.website/#/profile" class="button">View Your Reservation</a> <!-- Replace with actual link if needed -->

        <p class="footer">Thank you for choosing us!</p>
    </div>
</body>
</html>
<?php /**PATH C:\Users\Ferdinand\Desktop\opticalcare\resources\views/emails/accept.blade.php ENDPATH**/ ?>