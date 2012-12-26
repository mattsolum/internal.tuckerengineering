<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Log In</title>

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

    <script src="<?PHP echo base_url(); ?>resources/js/jquery.js" type="text/javascript"></script>
    <script src="<?PHP echo base_url(); ?>resources/js/jquery.formLabels1.0.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(function(){
            $.fn.formLabels();
        });
    </script>

        <link rel="stylesheet" href="<?PHP echo base_url(); ?>resources/css/style.css">
    </head>
    <body id="log_in">
        <form method="POST">
            <input type="text" name="email" title="Email Address" value="" />
            <input type="password" name="password" title="Password" value="" /> 
        </form>
    </body>
</html>
