{*
fafb6e680e507242d4d3dc09c88872c868cb70b4, v1 (xcart_4_5_0), 2012-04-06 10:32:27, config.tpl, ferz
vim: set ts=2 sw=2 sts=2 et:

TaxCloud module settings template
*}

<p>TaxCloud is a highly available, secure, remotely invoked web service that gives you complete control over the presentation and reconciliation of all the steps in managing sales taxes for your customers' purchases.</p>

<p><b>All of TaxCloud's services are available to X-Cart merchants completely free of charge.</b> This is possible because TaxCloud fund it operation based on commissions paid by U.S. state governments.</p>

{capture name="taxcloud_note"}
{include file="modules/TaxCloud/config_note.tpl"}
{/capture}

{include file="main/tooltip_js.tpl" title="See TaxCloud configuration instructions" text=$smarty.capture.taxcloud_note id="taxcloud_note_tooltip" width=800 sticky=true}

<p><b>Note:</b> TaxCloud service is available for U.S. merchants only at the moment.</p>

<p><b>Note 2:</b> The installation of the TaxCloud module will replace the default tax management functionality of X-Cart.</p>

