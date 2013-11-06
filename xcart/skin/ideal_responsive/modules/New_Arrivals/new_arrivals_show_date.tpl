{*
8ec8b858917c921a726e2197071ce74d020e5ead, v1 (xcart_4_6_0), 2013-04-02 16:43:59, new_arrivals_show_date.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}

{if $new_arrivals_show_date eq 'Y'}
  <div class="new_arrivals_date">{$lng.lbl_added}:&nbsp;{$product.add_date|date_format:$config.Appearance.date_format}</div>
{/if}
