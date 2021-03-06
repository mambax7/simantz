<?php

class FormElement{
	
	public function FormElement(){
		
		}
		
	public function activateAutoComplete(){
		echo <<< EOF

	<script>
	function activateAutoComplete(){
	
		$(".autocomplete").each(function(index){
			link=$(this).attr('autocompleteurl');
			
			$(this).autocomplete({
			source:link,
			minLength: 0,
			autoFocus:true,		
			select: function( event, ui ) {
				var linkcol=$(this).attr('linkcolumn');
				var linktext=$(this).attr('linktext');
				
				if(document.getElementById(linkcol))
					document.getElementById(linkcol).value=ui.item.id;
				if(document.getElementById(linktext))
					document.getElementById(linktext).value=ui.item.label;
				$(this).change();
			}
			});
			
			$(this).click(function(){
				$(this).autocomplete("search",$(this).val());
				$(this).select();
				});


		});
	}
	$(document).ready(function(){
		activateAutoComplete();
		});
	</script>
EOF;
		}
	}
