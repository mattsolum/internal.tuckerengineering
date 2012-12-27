<?PHP
    if(!isset($redirect))
    {
        $redirect = '';
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Log In</title>

        <script src="<?PHP echo base_url(); ?>resources/js/jquery.js" type="text/javascript"></script>
        <script src="<?PHP echo base_url(); ?>resources/js/jquery.formLabels1.0.js" type="text/javascript"></script>

        <script type="text/javascript">
        $(function(){
            $.fn.formLabels();
        });
        </script>

        <link rel="stylesheet" href="<?PHP echo base_url(); ?>resources/css/reset.css">
        <link rel="stylesheet" href="<?PHP echo base_url(); ?>resources/css/style.css">
    </head>
    <body id="log_in">
        <span id="company_name"><?PHP echo(setting('company.name')); ?></span>
        <form name="login" method="POST" action="<?PHP echo(site_url('user/auth' . $redirect)) ?>" autocomplete="off">
            <h1>Log In</h1>
            <ul>
                <li>
                    <input type="text" name="email" id="email" title="Email Address" value="" autocomplete="off" />
                </li>
                <li>
                    <input type="password" name="password" id="password" title="Password" value="" autocomplete="off" /> 
                </li>
                <input type="submit" class="hide" />
            </ul>
        </form>
    </body>
</html>
