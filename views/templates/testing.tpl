<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $GLOBALS['lang']; ?>" lang="<?php echo $GLOBALS['lang']; ?>">
<head>
<style media="screen">
			@import url(../../../views/<?php echo $GLOBALS['dir_theme']; ?>css/process_browser.css);
		</style>
</head>
<body>
<table cellspacing="10" >
<th><center><?php echo __("Country Adapted items and extensions");?></center></th>
<tr ><td>&nbsp;</td></tr>
<?php
foreach ($adaptedItems as $activityLabel => $activityRes)
{
?>
<tr>
<td><a target="_top" class="jumpToLink" href="../../../index.php/processBrowser/jumpBack?processUri=<?php echo urlencode($processUri); ?>&activityUri=<?php echo urlencode($activityRes->uriResource); ?>&testing=true"><?php echo $activityLabel;?></a></td>
</tr>

<?php
}
?>
</table>

<table cellspacing="10" >
<th><center><?php echo __("All items");?></center></th>
<tr ><td>&nbsp;</td></tr>
<?php
foreach ($data as $activityLabel => $activityRes)
{
?>
<tr>
<td><a target="_top" class="jumpToLink" href="../../../index.php/processBrowser/jumpBack?processUri=<?php echo urlencode($processUri); ?>&activityUri=<?php echo urlencode($activityRes->uriResource); ?>&testing=true"><?php echo $activityLabel;?></a></td>
</tr>

<?php
}
?>
</table>
</body>
</html>