<?php 
    if (!isset($_SESSION['league_ad'])) {
?>
<script>
	var border_color = "black";
	function minimize_league_ad() {
		if ($("#league_ad > a").text() == "MINIMIZAR") {
			$("#header_league").css("display", "none");
			$("#error_league").css("display", "none");
			$("#league_ad").css({
				"height":		"275px",
				"width":		"275px",
				"top":			"8%",
				"left":			"2%",
				"margin-top":	"0",
				"margin-left":	"0",
				"border-color":	border_color
			});
			$("#league_ad > a").text("MAXIMIZAR");
		} else if ($("#league_ad > a").text() == "FECHAR")
			$("#league_ad").remove();
		else {
			$("#header_league").css("display", "block");
			$("#error_league").css("display", "block");
			$("#league_ad").css({
				"height": 		"240px",
				"width": 		"420px",
				"top": 			"50%",
				"left": 		"50%",
				"margin-top": 	"-110px",
				"margin-left": 	"-210px",
				"border-color": "black"
			});
			$("#league_ad > a").text("MINIMIZAR");
		}
	}
</script>
<div id="league_ad" style="width: 600px; height: 400px; position: fixed; top: 50%; left: 50%; z-index: 99999; margin-top: -270px; margin-left: -300px;">
	<a href="#" style="background-color: black; color: white; border-radius: 5px; padding: 5px;" onclick="$.ajax({url: './ajax.php?act=remove_league_ad', context: document.body}); $('#league_ad').remove(); return false;">FECHAR</a>
	<a href="./donate" style="text-decoration: none;" onclick="$.ajax({url: './ajax.php?act=remove_league_ad', context: document.body}); $('#league_ad').remove();">
		<img src="<?=$static_url;?>/images/promos/promo3.png" style="width:600px; height:500px"/>
	</a>
</div>

<?php } ?>