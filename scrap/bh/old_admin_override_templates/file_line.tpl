{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{default node_name=$node.name node_url=$node.url_alias}{$node.data_map.file.content.mime_type|mimetype_icon( small, $node.object.content_class.name )}&nbsp;{section show=$node_url}<a href={$node_url|ezurl}>{/section}{$node_name|wash}{section show=$node_url}</a>{/section}{/default}