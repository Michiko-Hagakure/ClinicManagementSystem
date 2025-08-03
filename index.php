<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to My Website</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
            background-color: #f0f0f0;
        }
        h1 {
            color: #3366cc;
        }
        .random-number {
            font-size: 24px;
            font-weight: bold;
            color: #ff6600;
            margin: 20px;
        }
    </style>
</head>
<body>
    <h1>Welcome to My PHP Page</h1>
    
    <?php
    // Generate a random number
    $randomNumber = rand(1, 100);
    echo "<div class='random-number'>Your lucky number is: $randomNumber</div>";
    
    // Display current date and time
    date_default_timezone_set('UTC');
    echo "<p>Current date and time: " . date('Y-m-d H:i:s') . "</p>";
    
    // Random greeting
    $greetings = ["Hello", "Welcome", "Greetings", "Hi there", "Howdy"];
    $randomGreeting = $greetings[array_rand($greetings)];
    echo "<p>$randomGreeting, visitor!</p>";
    ?>
    
    <p><a href="about.php">Learn more about us</a></p>
</body>
</html>