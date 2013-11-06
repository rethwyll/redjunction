{*
22f6923a9e65090657fef8e74ebb5436d9e861ee, v4 (xcart_4_6_0), 2013-05-23 14:12:07, bottom.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
<div class="box">
	<img src="{$ImagesDir}/spacer.gif" alt="" id="left-c" />
	<img src="{$ImagesDir}/spacer.gif" alt="" id="right-c" />
  <div class="subbox">
    {if $active_modules.Klarna_Payments}
      {include file="modules/Klarna_Payments/footer_logo.tpl"}
    {/if}
    <div class="left">{include file="copyright.tpl"}</div>
    <div class="right">{include file="main/prnotice.tpl"}</div>
  </div>
</div>
