<html>
<head>

	<meta charset="UTF-8">
	<?php if($page_data->noindex) : ?>
	<meta name="robots" content="noindex">
	<?php endif; ?>
	<!-- Google+ Sharing-->
	<meta itemprop="name" content="<?php the_title( ); ?>">
	<meta itemprop="description" content="<?php echo $page_data->sh_text;?>">
	<meta itemprop="image" content="">
	
	<!-- Twitter Card data -->
	<meta name="twitter:card" content="summary">
	<meta name="twitter:site" content="<?php echo get_option('twitter_author');?>">
	<meta name="twitter:title" content="<?php the_title( ); ?>">
	<meta name="twitter:description" content="<?php echo $page_data->sh_text;?>">
	<meta name="twitter:creator" content="<?php echo get_option('twitter_author');?>">
	
	<!-- Open Graph data -->
	<meta property="og:title" content="<?php the_title( ); ?>" />
	<meta property="og:type" content="article" />
	<meta property="og:url" content="<?php echo $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; ?>"/>
	<meta property="og:image" content="" />
	<meta property="og:description" content="<?php echo $page_data->sh_text;?>" />
	<meta property="og:site_name" content="<?php bloginfo('name'); ?>"/>

	<title><?php the_title( ); ?></title>
	
	<?php if(is_user_logged_in()) : ?>
		<?php $barClass .= " logged"?>
		<style>
		html { margin-top: 32px !important; }
		* html body { margin-top: 32px !important; }
		@media screen and ( max-width: 782px ) {
			html { margin-top: 46px !important; }
			* html body { margin-top: 46px !important; }
		}
		</style>
	<?php endif; ?>
	</head>

<body>
	<?php  wp_footer();  ?>
	<div class="<?php echo $barClass;?>" style="background: <?php echo $page_data->bar_color ?>">
		<?php if($page_data->bar_text) : ?>
		<div class="bar_text" <?php echo ($page_data->textline) ? 'style="width:100%;margin: 5px 0px;"' : ""?>>
			<span style="color:<?php echo $page_data->text_color ?>" ><?php echo $page_data->bar_text ?></span>
		</div>
		<?php endif; ?>
		<?php if($page_data->form) : ?>
		<div class="bar_form">
			<?php echo stripslashes_deep($page_data->form) ?>
		</div>
		<?php endif; ?>
		<?php if(!empty($page_data->button_1_link ) || !empty($page_data->button_2_link )) : ?>
		<div class="extend_buttons">
			<?php if(!empty($page_data->button_1_link )) : ?>
				<a href="<?php echo $page_data->button_1_link ?>" class="btn" style="background-color: <?php echo $page_data->button_1_color ?>" id="pdf_button_1"><?php echo $page_data->button_1_text ?></a>
			<?php endif; ?>
			<?php if(!empty($page_data->button_2_link )) : ?>
				<a href="<?php echo $page_data->button_2_link ?>" class="btn btn" style="background-color: <?php echo $page_data->button_2_color ?>" id="pdf_button_2"><?php echo $page_data->button_2_text ?></a>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<?php if($share->isButtons()) : ?>
		<div class="share-container" <?php echo $share->getButtonsWidth() ?> >
			<?php echo $share->getButtons()?>
		</div>
		<?php endif; ?>
	</div>
	<div id="pdf_document_wrap" class="<?php echo $document_class?>">
		<div id="viewerContainer">
			<div id="viewer" class="pdfViewer">
			
			</div>
		</div>
	</div>
<script>
	var DEFAULT_URL = '<?php echo $page_data->pdf_url;?>';
	var loadedDocument = "";
	if (!PDFJS.PDFViewer || !PDFJS.getDocument) {
	  jQuery('#viewer').html('<h2 class="error">Please build the library and components<br> using `node make generic components`</h2>');
	}
	else if (!DEFAULT_URL) {
		jQuery('#viewer').html('<h2 class="error">Undefined PDF file</h2>');
	}
	else {
		init()
	}
	function init() {
		//init pdfViewer
		var container = document.getElementById('viewerContainer');
		var pdfViewer = new PDFJS.PDFViewer({
			container: container
		});
		
		container.addEventListener('pagesinit', function () {
			// we can use pdfViewer now, e.g. let's change default scale.
			pdfViewer.currentScaleValue = 'auto';
		});
		window.onresize = function(event) {
			pdfViewer.setDocument(loadedDocument);
			
		};
		// Loading document.
		PDFJS.getDocument(DEFAULT_URL).then(function (pdfDocument) {
			loadedDocument = pdfDocument;
			pdfViewer.setDocument(pdfDocument);
		});
	}
</script>

<script>
	jQuery(function($) {
		<?php if(!empty($page_data->button_2_link )) : ?>
			trackButton(2);
		<?php endif; ?>
		<?php if(!empty($page_data->button_1_link )) : ?>
		   trackButton(1);
		<?php endif; ?>
	});
</script>

</body>
</html>
