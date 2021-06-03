<!DOCTYPE html>
<html>
    <head>
        <title>Ashish Maheshwari MD5 Cracker</title>
        <style>
            p {
                font-family: Arial,sans-serif,serif;
            }
            h1 {
                font-family: Arial,sans-serif,serif;
            }
            a {
                font-family: Arial,sans-serif,serif;
            }
        </style>
    </head>
    <body>
        <h1><b>MD5 cracker</b></h1>
        <p>This application takes an MD5 hash of a four digit pin and check all 10,000 possible four digit PINs to determine the PIN.</p>
            <?php
            echo "<pre>";
            echo "Debug Output:\n";
            $goodtext = "Not found";
            if ( isset($_GET['md5']) ) {
                $time_pre = microtime(true);
                $md5 = $_GET['md5'];

                $txt = "0123456789";
                $show = 15;
                $count = 0;

                for($i=0; $i<strlen($txt); $i++ ) {
                    $ch1 = $txt[$i];  
                    
                    for($j=0; $j<strlen($txt); $j++ ) {
                        $ch2 = $txt[$j]; 

                        for($k=0; $k<strlen($txt); $k++ ) { 
                            $ch3 = $txt[$k];

                            for($l=0; $l<strlen($txt); $l++ ) {
                                $ch4 = $txt[$l];
                                $try = $ch1.$ch2.$ch3.$ch4;
                                $count = $count + 1;
                                $check = hash('md5', $try);
                                if ( $check == $md5 ) {
                                    $goodtext = $try;
                                    break;   
                                }

                                if ( $show > 0 ) {
                                    print "$check $try\n";
                                    $show = $show - 1;
                                }
                            }
                        }
                    }
                }
                $time_post = microtime(true);
                print "Total checks: ";
                print $count;
                print "\nEllapsed time: ";
                print $time_post-$time_pre;
                print "\n";
            }
            echo "</pre>"
            ?>
        </pre>
        <p>PIN: <?= htmlentities($goodtext); ?></p>
        <form>
            <input type="text" name="md5" size="40" />
            <input type="submit" value="Crack MD5"/>
        </form>
        <ul>
            <li><a href="index.php">Reset</a></li>
            <li><a href="makepin.php">Make an MD5 PIN</a></li>
            <li><a href="md5.php">MD5 Encoder</a></li>
        </ul>
    </body>
</html>