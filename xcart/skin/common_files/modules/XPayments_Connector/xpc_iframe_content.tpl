{*
3c9588e9c662fa842f0cc1bf11fb450c44cf1dc7, v1 (xcart_4_5_2), 2012-07-20 09:58:47, xpc_iframe_content.tpl, tito
vim: set ts=2 sw=2 sts=2 et:
*}
<!DOCTYPE html>
<html>
<head></head>

<body>
{* Draws a place order form and submits it to payment_cc.php *}


<form name="xpc_form" id="xpc_form" action="{$action}" method="post">
    {foreach from=$fields key="name" item="value"}
        <input type="hidden" name="{$name}" value="{$value}" />
    {/foreach}
</form>

{* Ok, we're ready to place order now *}

{literal}
<script type="text/javascript">

document.getElementById('xpc_form').submit();

</script>
{/literal}

</body>
</html>
