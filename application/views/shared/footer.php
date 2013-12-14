<?php 
    $base_url = $this->config->item('base_url');
    $assets = $this->config->item('assets');
?>

 <script>
  document.write('<script src=' +
  ('__proto__' in {} ? '<?php echo $assets; ?>javascripts/vendor/zepto' : '<?php echo $assets; ?>javascripts/vendor/jquery') +
  '.js><\/script>')
  </script>
  
  <script src="<?php echo $assets; ?>javascripts/foundation/foundation.js"></script>
	
	<script src="<?php echo $assets; ?>javascripts/foundation/foundation.abide.js"></script>
	
	<script src="<?php echo $assets; ?>javascripts/foundation/foundation.alerts.js"></script>
	
	<script src="<?php echo $assets; ?>javascripts/foundation/foundation.clearing.js"></script>
	
	<script src="<?php echo $assets; ?>javascripts/foundation/foundation.cookie.js"></script>
	
	<script src="<?php echo $assets; ?>javascripts/foundation/foundation.dropdown.js"></script>
	
	<script src="<?php echo $assets; ?>javascripts/foundation/foundation.forms.js"></script>
	
	<script src="<?php echo $assets; ?>javascripts/foundation/foundation.interchange.js"></script>
	
	<script src="<?php echo $assets; ?>javascripts/foundation/foundation.joyride.js"></script>
	
	<script src="<?php echo $assets; ?>javascripts/foundation/foundation.magellan.js"></script>
	
	<script src="<?php echo $assets; ?>javascripts/foundation/foundation.orbit.js"></script>
	
	<script src="<?php echo $assets; ?>javascripts/foundation/foundation.placeholder.js"></script>
	
	<script src="<?php echo $assets; ?>javascripts/foundation/foundation.reveal.js"></script>
	
	<script src="<?php echo $assets; ?>javascripts/foundation/foundation.section.js"></script>
	
	<script src="<?php echo $assets; ?>javascripts/foundation/foundation.tooltips.js"></script>
	
	<script src="<?php echo $assets; ?>javascripts/foundation/foundation.topbar.js"></script>
	
  
  <script>
    $(document).foundation();
  </script>
</body>
</html>
