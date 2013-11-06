{*
bb77a8bec54e8fd7c6db90461e8b03abaa0a8955, v5 (xcart_4_6_0), 2013-05-28 14:27:05, help_contactus.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
{include file="mail/html/mail_header.tpl"}
<br />{$lng.eml_customers_need_help}

<br /><b>{$lng.lbl_customer_info}:</b>

<hr size="1" noshade="noshade" />

<table cellpadding="2" cellspacing="0">
{if $is_areas.C}

{if $default_fields.company.avail eq 'Y'}
<tr>
<td><b>{$lng.lbl_company}:</b></td>
<td>&nbsp;</td>
<td>{$contact.company}</td>
</tr>
{/if}
{if $default_fields.firstname.avail eq 'Y'}
<tr>
<td><b>{$lng.lbl_first_name}:</b></td>
<td>&nbsp;</td>
<td>{$contact.firstname}</td>
</tr>
{/if}
{if $default_fields.lastname.avail eq 'Y'}
<tr>
<td><b>{$lng.lbl_last_name}:</b></td>
<td>&nbsp;</td>
<td>{$contact.lastname}</td>
</tr>
{/if}

{/if}

{if $is_areas.A}
<tr>
<td colspan="3"><b>{$lng.lbl_address}:</b></td>
</tr>

<tr>
<td colspan="3">
<table cellpadding="1" cellspacing="0">
{if $default_fields.b_address.avail eq 'Y' or $default_fields.b_address_2.avail eq 'Y'}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_address}:</b></td>
<td>&nbsp;</td>
<td>{$contact.b_address}<br />{$contact.b_address_2}</td>
</tr>
{/if}
{if $default_fields.b_city.avail eq 'Y'}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_city}:</b></td>
<td>&nbsp;</td>
<td>{$contact.b_city}</td>
</tr>
{/if}
{if $default_fields.b_county.avail eq 'Y' and $config.General.use_counties eq "Y"}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_county}:</b></td>
<td>&nbsp;</td>
<td>{$contact.b_countyname}</td>
</tr>
{/if}
{if $default_fields.b_state.avail eq 'Y'}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_state}:</b></td>
<td>&nbsp;</td>
<td>{$contact.b_statename}</td>
</tr>
{/if}
{if $default_fields.b_country.avail eq 'Y'}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_country}:</b></td>
<td>&nbsp;</td>
<td>{$contact.b_countryname}</td>
</tr>
{/if}
{if $default_fields.b_zipcode.avail eq 'Y'}
<tr>
<td>&nbsp;&nbsp;&nbsp;</td>
<td><b>{$lng.lbl_zip_code}:</b></td>
<td>&nbsp;</td>
<td>{include file="main/zipcode.tpl" val=$contact.b_zipcode zip4=$contact.b_zip4 static=true}</td>
</tr>
{/if}
</table>
</td>
</tr>

{if $default_fields.phone.avail eq 'Y'}
<tr>
<td><b>{$lng.lbl_phone}:</b></td>
<td>&nbsp;</td>
<td>{$contact.phone}</td>
</tr>
{/if}
{if $default_fields.fax.avail eq 'Y'}
<tr>
<td><b>{$lng.lbl_fax}:</b></td>
<td>&nbsp;</td>
<td>{$contact.fax}</td>
</tr>
{/if}
{if $default_fields.email.avail eq 'Y'}
<tr>
<td><b>{$lng.lbl_email}:</b></td>
<td>&nbsp;</td>
<td>{$contact.email}</td>
</tr>
{/if}
{if $default_fields.url.avail eq 'Y'}
<tr>
<td><b>{$lng.lbl_web_site}:</b></td>
<td>&nbsp;</td>
<td>{$contact.url}</td>
</tr>
{/if}
{/if}

{if $additional_fields ne ''}
{foreach from=$additional_fields item=v}
<tr>
<td><b>{$v.title}:</b></td>
<td>&nbsp;</td>
<td>{$v.value}</td>
</tr>
{/foreach}
{/if}

{if $default_fields.department.avail eq 'Y'}
<tr>
<td><b>{$lng.lbl_department}:</b></td>
<td>&nbsp;</td>
<td>{$contact.department}</td>
</tr>
{/if}
<tr>
<td><b>{$lng.lbl_subject}:</b></td>
<td>&nbsp;</td>
<td>{$contact.subject}</td>
</tr>
<tr>
<td colspan="3"><b>{$lng.lbl_message}:</b><br /><hr size="1" noshade="noshade" color="#DDDDDD" align="left" /></td>
</tr>
<tr>
<td colspan="3">{$contact.body|nl2br}</td>
</tr>
</table>

{include file="mail/html/signature.tpl"}
