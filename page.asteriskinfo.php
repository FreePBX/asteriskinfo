<?php
	if (!defined('FREEPBX_IS_AUTH')) { die('No direct script access allowed'); }
	echo \FreePBX::Asteriskinfo()->showPage("asteriskinfo");