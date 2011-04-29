	<script type="text/php">

	if ( isset($pdf) ) {
	
	  // Open the object: all drawing commands will
	  // go to the object instead of the current page
	  $footer = $pdf->open_object();
	
	  $w = $pdf->get_width();
	  $h = $pdf->get_height();
	
	  // Draw a line along the bottom
	  $y = $h - 2 * $text_height - 24;
	  $pdf->line(16, $y, $w - 16, $y, $color, 1);
	
	  // Add an initals box
	  $font = Font_Metrics::get_font("helvetica", "bold");
	  $text = "Initials:";
	  $width = Font_Metrics::get_text_width($text, $font, $size);
	  $pdf->text($w - 16 - $width - 38, $y, $text, $font, $size, $color);
	  $pdf->rectangle($w - 16 - 36, $y - 2, 36, $text_height + 4, array(0.5,0.5,0.5), 0.5);
	
	  // Add a logo
	  $img_w = 2 * 72; // 2 inches, in points
	  $img_h = 1 * 72; // 1 inch, in points -- change these as required
	  $pdf->image("print_logo.png", "png", ($w - $img_w) / 2.0, $y - $img_h, $img_w, $img_h);
	
	  // Close the object (stop capture)
	  $pdf->close_object();
	
	  // Add the object to every page. You can
	  // also specify "odd" or "even"
	  $pdf->add_object($footer, "all");
	}
	
	</script>