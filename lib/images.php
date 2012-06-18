<?php

namespace images;

function resize($src, $dest, $width_new, $height_new, $crop = false)
{
	$extension = null;
	
	if (preg_match('/^(.+)\.(jpg|gif|png)$/', $src, $matches))
	{
		$extension = $matches[2];
	}
	else
	{
		if ($info = @getimagesize($src))
		{
			if (preg_match('/^image\/(jpg|gif|png)$/', $info['mime'], $matches))
			{
				$extension = $matches[1];
			}
		}
		
	}
	
	$image = null;
	
	switch ((string)$extension)
	{
		case 'gif':
			$image = @imagecreatefromgif($src);
			break;
		case 'png':
			$image = @imagecreatefrompng($src);
			break;
		case 'jpg':
		default:
			$image = @imagecreatefromjpeg($src);
	}
	
	if (!$image)
	{
		return false;
	}
	
	$image_new = imagecreatetruecolor($width_new, $height_new);
	
	list($width_orig, $height_orig) = getimagesize($src);
	list($width_prop, $height_prop) = calc_prop_size($width_orig, $height_orig, $width_new, $height_new, $crop);
	
	$bg_color = imagecolorallocate($image_new, 255, 255, 255);
	if (!$crop)
	{
		imagefill($image_new, 0, 0, $bg_color);
	}
	$start_w = ($width_new - $width_prop) / 2;
	$start_h = ($height_new - $height_prop) / 2;
	imagecopyresampled(
		$image_new, $image, //dst, src
		$start_w, $start_h, //dst x, y
		0, 0, //src x, y
		$width_prop, $height_prop, //dst w, h
		$width_orig, $height_orig //src w, h
	);
	
	imagejpeg($image_new, $dest, 90);
	
	return true;
}

function calc_prop_size($width_orig, $height_orig, $width_new, $height_new, $crop)
{
	$ratio_orig = $width_orig / $height_orig;
	
	if ($crop)
	{
		$cond = $width_new / $height_new <= $ratio_orig;
	}
	else
	{
		$cond = $width_new / $height_new > $ratio_orig;
	}
	
	if ($cond)
	{
		$width_new = $height_new * $ratio_orig;
	}
	else
	{
		$height_new = $width_new / $ratio_orig;
	}
	
	return array($width_new, $height_new);
}