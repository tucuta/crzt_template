<?php
// No direct access.
defined('_JEXEC') or die;

require_once('lib/crzt.php');

$crzt = new CRZT($this);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml" >
<head>
	<jdoc:include type="head" />
	
	<?php	$crzt->addtohead();	?>
</head>
<body>


	<div class="container">
		<?php	$crzt->positions('top', 'row');	?>
		<?php	$crzt->positions('header', 'row');	?>
		
		<?php
		if($crzt->modules("showcase-a")){
		?>
		</div>
		<?	$crzt->positions('showcase', 'row-fluid');	?>
		<div class="container">
		<?php	
		}
		?>
		<?php	$crzt->positions('featured', 'row');	?>
		<?php	$crzt->positions('utility', 'row');	?>
		<?php	$crzt->positions('breadcrumb', 'row');	?>
		

		<?php	$crzt->mainbody();	?>
		

		<?php	$crzt->positions('extension', 'row');	?>
		<?php	$crzt->positions('bottom', 'row');	?>

		<?php
		if($crzt->modules("footer-b")){
		?>
		</div>
		<div id="footer-out">
		<div class="container">
		<?	$crzt->positions('footer', 'row');	?>
		</div>
		</div>
		<div class="container">
		<?php	
		}
		?>
		<?php	$crzt->positions('copyright', 'row');	?>
	</div>	

       	<jdoc:include type="modules" name="debug" />
	
	<?php	$crzt->cookie();	?>

	<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/lib/jquery/jquery-1.9.1.min.js"></script>
	<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/lib/bootstrap/js/bootstrap.js"></script>
	<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/js/script.js"></script>
</body>
</html>
