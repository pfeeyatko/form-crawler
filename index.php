<?php

require_once 'goutte-v2.0.4.phar';

use Goutte\Client;

if (isset($_POST['submit'])) {
    $url = empty($_POST['url']) ? $_SERVER['HTTP_REFERER'] : $_POST['url'];

    $client = new Client();

    try {
        $crawler = $client->request('GET', $url);
    } catch (Exception $e) {
        $error = $e;
    }
}

?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Form crawler</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
  </head>

  <body>

    <div class="container">
        <h1 class="page-header">Form crawler</h1>

        <p>Enter a URL below to a web page with a form on it. This script will then crawl the page and extract all of the form fields and related information.</p>

        <div class="well">
            <form class="form-inline" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
              <div class="form-group">
                <label for="url">Website</label>
                <input type="text" class="form-control" id="url" name="url" placeholder="http://reborn.co" required>
              </div>
              <button type="submit" class="btn btn-primary" name="submit">Crawl</button>
            </form>
        </div>

        <?php if (!isset($error)): ?>

            <?php if (isset($crawler)): ?>
                <pre><?php
                        $crawler->filter('input')->each(function($node) {

                            $req = is_null($node->attr('required')) ? '' : ' *Required';

                            switch ($node->attr('type')) {
                                case 'hidden':
                                    break;
                                case 'radio':
                                case 'checkbox':
                                    echo 'Input field: ' . $node->attr('name') . ', value: ' . $node->attr('value') . ', type: ' . $node->attr('type') . $req . PHP_EOL;
                                    break;
                                default:
                                    echo 'Input field: ' . $node->attr('name') . ', type: ' . $node->attr('type') . $req . PHP_EOL;
                                    break;
                            }

                        });

                        $crawler->filter('select')->each(function($node) {
                            $req = is_null($node->attr('required')) ? '' : ' *Required';

                            echo 'Select field: ' . $node->attr('name') .  $req . ', Options: ' . PHP_EOL;

                            foreach ($node->children() as $option) {
                                echo '    ' . $option->nodeValue . PHP_EOL;
                            }
                        });

                        $crawler->filter('textarea')->each(function($node) {
                            $req = is_null($node->attr('required')) ? '' : ' *Required';

                            echo 'Form field: ' . $node->attr('name') . ', type: textarea' . $req . PHP_EOL;
                        });
                    ?>
                </pre>
            <?php endif; ?>

        <?php else: ?>

            <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>

        <?php endif; ?>
    </div>

  </body>
</html>
