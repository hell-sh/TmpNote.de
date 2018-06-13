<?php
$title="Create free, end-to-end encrypted notes";
$description="TmpNote.de is a free and open-source service for end-to-end encrypted notes and code snippets.";
require"src/header.php";
?>
<div id="tmpnote-container">
	<div id="tmpnote-header">
		<h1>TmpNote<small>.de</small></h1>
		<p>A free and open-source service for end-to-end encrypted notes and code snippets.</p>
		<p>Copyright (c) 2018, <a href="https://hellsh.com" target="_blank">Hellsh Ltd.</a> &middot; <a href="https://hellsh.com/privacy" target="_blank">Privacy Policy</a> &middot; <a href="#modal-open-source" uk-toggle>Open Source</a></p>
	</div>
	<div id="tmpnote-card" class="uk-card uk-card-default">
		<div id="tmpnote-created" class="uk-card-body uk-hidden">
			<div class="card-content">
				<h2 id="tmpnote-created-state">Loading...</h2>
				<span id="tmpnote-created-cont" class="uk-hidden uk-animation-fade">
					<input id="tmpnote-created-url" type="text" class="uk-input" onfocus="this.select()" readonly>
					<p>Your TmpNote has been encrypted and uploaded in an encrypted manner. Only with the key (behind the #), it is possible to decrypt and read it.</p>
				</span>
			</div>
		</div>
		<div id="tmpnote-editor" style="min-height:200px"></div>
		<div id="tmpnote-tools" class="uk-card-footer">
			<a id="tmpnote-action" class="uk-button uk-button-small uk-button-primary">Create TmpNote</a>
			<b> Formatting </b>
			<select id="tmpnote-formatting">
				<?php
				foreach(json_decode(file_get_contents("formattings.json"), true) as $formatting)
				{
					echo '<option value="ace/mode/'.$formatting["value"].'">'.$formatting["name"].'</option>';
				}
				?>
			</select>
			<b> Expires in </b>
			<input id="tmpnote-time" type="number" min="1" value="30" max="60" style="width:35px;margin:0">
			<select id="tmpnote-timeunit">
				<option value="60" data-singular="Minute">Minutes</option>
				<option value="3600" data-singular="Hour">Hours</option>
				<option value="86400" data-singular="Day">Days</option>
				<option value="2592000" data-singular="Month">Months</option>
			</select>
			<span> </span>
			<input id="tmpnote-deleteonview" type="checkbox" class="uk-checkbox"><label for="tmpnote-deleteonview" style="cursor:pointer"> Delete after View</label>
		</div>
	</div>
</div>
<script>
	onEditorReady=function()
	{
		var hash=location.hash.replace("#","");
		if(hash=="unknown")
		{
			alert("The TmpNote you tried to access is unknown to us. (Already expired?)");
			location.hash="";
		}
		$(window).on("beforeunload",function()
		{
			if(!$("#tmpnote-editor").hasClass("uk-hidden"))
			{
				setData("tmpnote_formatting",$("#tmpnote-formatting").val().substr(9));
				setData("tmpnote_cont",editor.getValue());
			}
		});
		editor = ace.edit("tmpnote-editor");
		editor.setTheme("ace/theme/monokai");
		if(getData("tmpnote_cont")!=null)
		{
			editor.setValue(getData("tmpnote_cont"));
		}
		editor.commands.addCommand({
			name: 'Save',
			bindKey: {win: 'Ctrl-S', mac: 'Command-S'},
			exec: function(editor)
			{
				setData("tmpnote_cont",editor.getValue());
				UIkit.notification({
					message: data.error,
					status: "success",
					pos: "top-center",
					timeout: 1000
				});
			},
		});
		if(getData("tmpnote_formatting")!=null)
		{
			$("#tmpnote-formatting [value='ace/mode/"+getData("tmpnote_formatting")+"']").attr("selected","selected");
		}
		$("#tmpnote-formatting").on("change",function()
		{
			editor.getSession().setMode($("#tmpnote-formatting").val());
			setData("tmpnote_formatting",$("#tmpnote-formatting").val().substr(9));
		}).change();
		$("#tmpnote-timeunit option").each(function()
		{
			$(this).attr("data-plural", $(this).html());
		});
		$("#tmpnote-time").on("input", function()
		{
			if($(this).val()==1)
			{
				$("#tmpnote-timeunit option").each(function()
				{
					$(this).html($(this).attr("data-singular"));
				});
			} else
			{
				$("#tmpnote-timeunit option").each(function()
				{
					$(this).html($(this).attr("data-plural"));
				});
			}
		});
		$("#tmpnote-action").on("click",function()
		{
			var time=Math.round($("#tmpnote-time").val()*parseInt($("#tmpnote-timeunit").val()));
			if(time<180||time>31104000)
			{
				UIkit.notification({
					message: "Your TmpNote must expire in 12 months or less and must exist for at least 3 minutes.",
					status: "danger",
					pos: "top-center",
					timeout: 5000
				});
			}
			else
			{
				var key=encrypted="";
				if(editor.getValue() != "")
				{
					for(var i=0;i<8;i++)
					{
						switch(rand(1,3))
						{
							case 1:
							key+=String.fromCharCode(rand(45,58));
							/*key+=String.fromCharCode(rand(48,57));*/
							break;
							case 2:
							key+=String.fromCharCode(rand(65,90));
							break;
							case 3:
							key+=String.fromCharCode(rand(97,122));
							break;
						}
					}
					encrypted=CryptoJS.TripleDES.encrypt("TmpNote"+editor.getValue(),CryptoJS.enc.Utf8.parse(key),{
						mode:CryptoJS.mode.ECB,
						padding:CryptoJS.pad.Pkcs7
					});
					if(encrypted.length>32000)
					{
						UIkit.notification({
							message: "Your note can't be bigger than 32 KB.",
							status: "danger",
							pos: "top-center",
							timeout: 3000
						});
					}
					else
					{
						$("#tmpnote-action").addClass("uk-hidden");
						$("#tmpnote-created").removeClass("uk-hidden");
						$("#tmpnote-tools").addClass("uk-hidden");
						$("#tmpnote-editor").addClass("uk-hidden");
						$.post("/api/create",
						{
							"formatting": $("#tmpnote-formatting").val().substr(9),
							"time": time,
							"encrypted": encrypted.toString(),
							"type": ($("#tmpnote-deleteonview").is(":checked")?1:0)
						}).done(function(data)
						{
							if(data.error!==undefined)
							{
								UIkit.notification({
									message: data.error,
									status: "danger",
									pos: "top-center",
									timeout: 4000
								});
							}
							if(data.id!==undefined)
							{
								$("#tmpnote-created-state").text("TmpNote created.");
								$("#tmpnote-created-url").val("https://tmpnote.de/"+data.id+"#"+key)[0].focus();
								$("#tmpnote-created-cont").removeClass("uk-hidden");
								unsetData("tmpnote_cont");
								unsetData("tmpnote_formatting")
							}
							else
							{
								$("#tmpnote-action").removeClass("uk-hidden");
								$("#tmpnote-editor").removeClass("uk-hidden");
								$("#tmpnote-created").addClass("uk-hidden");
								$("#tmpnote-tools").removeClass("uk-hidden");
							}
						})
					}
				}
				else
				{
					UIkit.notification({
						message: "Your TmpNote can't be blank.",
						status: "danger",
						pos: "top-center",
						timeout: 2000
					});
				}
			}
		});
	};
</script>
<?require"src/footer.php";
