{*
1fe9c437ef64e78b0b9c259b1fd275cbee43bc20, v1 (xcart_4_5_3), 2012-08-06 14:02:13, banner_effect_selector.tpl, random
vim: set ts=2 sw=2 sts=2 et:
*}
<select name="{$name}">
  <option value="fade" {if $selected_effect eq 'fade'} selected="selected"{/if}>Fade</option>
  <option value="wipe" {if $selected_effect eq 'wipe'} selected="selected"{/if}>Wipe</option>
  <option value="scrollDown" {if $selected_effect eq 'scrollDown'} selected="selected"{/if}>ScrollDown</option>
  <option value="scrollUp" {if $selected_effect eq 'scrollUp'} selected="selected"{/if}>ScrollUp</option>
  <option value="fadeZoom" {if $selected_effect eq 'fadeZoom'} selected="selected"{/if}>FadeZoom</option>
  <option value="cover" {if $selected_effect eq 'cover'} selected="selected"{/if}>Cover</option>
  <option value="blindX" {if $selected_effect eq 'blindX'} selected="selected"{/if}>BlindX</option>
  <option value="blindY" {if $selected_effect eq 'blindY'} selected="selected"{/if}>BlindY</option>
  <option value="blindZ" {if $selected_effect eq 'blindZ'} selected="selected"{/if}>BlindZ</option>
  <option value="curtainX" {if $selected_effect eq 'curtainX'} selected="selected"{/if}>CurtainX</option>
  <option value="curtainY" {if $selected_effect eq 'curtainY'} selected="selected"{/if}>CurtainY</option>
  <option value="growX" {if $selected_effect eq 'growX'} selected="selected"{/if}>GrowX</option>
  <option value="growY" {if $selected_effect eq 'growY'} selected="selected"{/if}>GrowY</option>
  <option value="none" {if $selected_effect eq 'none'} selected="selected"{/if}>None</option>
  <option value="scrollLeft" {if $selected_effect eq 'scrollLeft'} selected="selected"{/if}>ScrollLeft</option>
  <option value="scrollRight" {if $selected_effect eq 'scrollRight'} selected="selected"{/if}>ScrollRight</option>
  <option value="scrollHorz" {if $selected_effect eq 'scrollHorz'} selected="selected"{/if}>ScrollHorz</option>
  <option value="scrollVert" {if $selected_effect eq 'scrollVert'} selected="selected"{/if}>ScrollVert</option>
  <option value="shuffle" {if $selected_effect eq 'shuffle'} selected="selected"{/if}>Shuffle</option>
  <option value="slideX" {if $selected_effect eq 'slideX'} selected="selected"{/if}>SlideX</option>
  <option value="slideY" {if $selected_effect eq 'slideY'} selected="selected"{/if}>SlideY</option>
  <option value="toss" {if $selected_effect eq 'toss'} selected="selected"{/if}>Toss</option>
  <option value="turnUp" {if $selected_effect eq 'turnUp'} selected="selected"{/if}>TurnUp</option>
  <option value="turnDown" {if $selected_effect eq 'turnDown'} selected="selected"{/if}>TurnDown</option>
  <option value="turnLeft" {if $selected_effect eq 'turnLeft'} selected="selected"{/if}>TurnLeft</option>
  <option value="turnRight" {if $selected_effect eq 'turnRight'} selected="selected"{/if}>TurnRight</option>
  <option value="uncover" {if $selected_effect eq 'uncover'} selected="selected"{/if}>Uncover</option>
  <option value="zoom" {if $selected_effect eq 'zoom'} selected="selected"{/if}>Zoom</option>
  <option value="random" {if $selected_effect eq 'random'} selected="selected"{/if}>Random</option>
</select>
