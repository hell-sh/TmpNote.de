<?php
http_response_code(200);
$title="An ephemeral, end-to-end encrypted note";
require"src/header.php";
?>
<div id="tmpnote-container">
	<div id="tmpnote-header">
		<h1>TmpNote<small>.de</small></h1>
		<p>A free and open-source service for end-to-end encrypted notes and code snippets.</p>
		<p>Copyright (c) 2018, <a href="https://hellsh.com" target="_blank">Hellsh Ltd.</a> &middot; <a href="https://hellsh.com/privacy" target="_blank">Privacy Policy</a> &middot; <a href="#modal-open-source" uk-toggle>Open Source</a></p>
	</div>
	<div id="tmpnote-card" class="uk-card uk-card-default">
		<div id="tmpnote-editor">It seems as tho you opened a TmpNote without knowing the key. Refresh this page if you do have the key.</div>
		<div id="tmpnote-tools" class="uk-card-footer">
			<a id="tmpnote-action" class="uk-button uk-button-small uk-button-primary">Fork this TmpNote</a>
			<b> Formatting </b>
			<select class="browser-default" style="border:0" id="tmpnote-formatting" disabled>
				<?php
				foreach(json_decode(file_get_contents("formattings.json"), true) as $formatting)
				{
					echo '<option value="'.$formatting["value"].'">'.$formatting["name"].'</option>';
				}
				?>
			</select>
			<span id="tmpnote-deleted" class="uk-hidden"> Your view caused this TmpNote to be deleted.</span>
			<span id="tmpnote-expires" class="uk-hidden"> <b>Expires on</b> <span>…</span></span>
		</div>
	</div>
</div>
<script src="https://cdn.hell.sh/TimeElements.js/1.0.1/TimeElements.js" integrity="sha384-c6XB8Gu93MHOo6LOG8Y9PlpUUKyKDvpVlSwcGWKdJd7JVdifnKM7hiu190mecbKk" crossorigin="anonymous"></script>
<script>
	var id=location.pathname.toString().substr(1),type,encrypted,editor;
	onEditorReady=function()
	{
		editor.setReadOnly(true);
		$.post("/api/info",
		{
			"id":id
		}).done(function(data)
		{
			if(data.type===undefined)
			{
				location.href="/#unknown";
			}
			else
			{
				type=data.type;
				$("#tmpnote-formatting [value='"+data.formatting+"']").attr("selected","selected");
				editor.getSession().setMode("ace/mode/"+$("#tmpnote-formatting").val());
				if(type==0)
				{
					encrypted=data.encrypted;
					$("#tmpnote-expires").removeClass("uk-hidden").find("span").attr("data-time", data.expires);
					calculateTimeElements();
				}
				var key=location.hash.toString().replace("#","");
				if(key.length==8)
				{
					attemptToDecrypt(key);
				}
				else
				{
					askForKey();
				}
			}
		});
	};
	askForKey=function(error)
	{
		var key;
		if(error===undefined)
		{
			key=prompt("What is the key to decrypt this Tmp Note?");
		}
		else
		{
			key=prompt("What is the key to decrypt this Tmp Note?", error);
		}
		if(key!==undefined&&key!==null)
		{
			if(key.indexOf("#")>-1)
			{
				key=key.split("#")[1];
			}
			if(key.length==8)
			{
				attemptToDecrypt(key);
			}
			else if(key != error && key != "")
			{
				askForKey("The key you've entered is incorrect.");
			}
		}
	};
	attemptToDecrypt=function(key)
	{
		if(type==0)
		{
			decryptAndShowNote(encrypted,key);
		}
		else if(type==1)
		{
			$.post("/api/read_and_delete",
			{
				"id":id,
				"key":key
			}).done(function(data)
			{
				if(data.encrypted===undefined)
				{
					if(data.error===undefined)
					{
						if(data.error==="TmpNote unknown or not type 1.")
						{
							location.href="/#unknown";
						}
						else
						{
							askForKey("The key you entered is incorrect.");
						}
					}
					else
					{
						askForKey(data.error);
					}
				}
				else
				{
					decryptAndShowNote(data.encrypted,key);
				}
			})
		}
	};
	decryptAndShowNote=function(encrypted,key)
	{
		var text=CryptoJS.TripleDES.decrypt({
			ciphertext:CryptoJS.enc.Base64.parse(encrypted)
		},CryptoJS.enc.Utf8.parse(key),{
			mode:CryptoJS.mode.ECB,
			padding:CryptoJS.pad.Pkcs7
		}).toString(CryptoJS.enc.Utf8);
		if(text.substr(0,7)=="TmpNote")
		{
			editor.setValue(text.substr(7),-1);
			if(type==1)
			{
				$("#tmpnote-deleted").removeClass("uk-hidden");
			}
			$("#tmpnote-action").on("click",function()
			{
				setData("tmpnote_formatting",$("#tmpnote-formatting").val());
				setData("tmpnote_cont",editor.getValue());
				location.href="/";
			});
		}
		else
		{
			askForKey("The key you entered is incorrect.");
		}
	}
</script>
<?require"src/footer.php";
