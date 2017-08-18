<!doctype html>
<html>
<head>
    <title>UNL Directory Prepared for <?php echo $context->user; ?> on <?php echo date('F jS, Y'); ?></title>
    <style>
        html {
            font-size: 12px;
            color: #000;
        }
        ol {
            list-style: none;
            padding-left: 1em;
        }
        a {
            text-decoration: none;
            color: #000;
        }
        abbr {
            text-decoration: none;
        }

        .on-campus-dialing + a {
            display: none;
        }
        .on-campus-label {
            display: none;
        }
    </style>
</head>
<body>
<h2>University of Nebraska–Lincoln Directory Prepared for <?php echo $context->user; ?> on <?php echo date('F jS, Y'); ?></h2>
<?php echo $savvy->render($context->output); ?>
