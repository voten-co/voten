<template>
	<div class="fond">
		<div class="contener_general">
			<div class="contener_mixte"><div class="ballcolor ball_1">&nbsp;</div></div>
			<div class="contener_mixte"><div class="ballcolor ball_2">&nbsp;</div></div>
			<div class="contener_mixte"><div class="ballcolor ball_3">&nbsp;</div></div>
			<div class="contener_mixte"><div class="ballcolor ball_4">&nbsp;</div></div>
		</div>
	</div>
</template>

<style>
	.fond{
		padding-top: 20%;
	    display: flex;
	    justify-content: center;
	    align-items: center;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}

	.contener_general
	{
	    -webkit-animation:animball_two 1s infinite;
	  	-moz-animation:animball_two 1s infinite;
	  	-ms-animation:animball_two 1s infinite;
	  	animation:animball_two 1s infinite;
	  	width:44px; height:44px;
	}
	.contener_mixte
	{
	  	width:44px; height:44px; position:absolute;
	}
	.ballcolor
	{
	  	width: 20px;
	  	height: 20px;
	  	border-radius: 50%;
	}
	.ball_1, .ball_2, .ball_3, .ball_4
	{
	  	position: absolute;
	  	-webkit-animation:animball_one 1s infinite ease;
	  	-moz-animation:animball_one 1s infinite ease;
	  	-ms-animation:animball_one 1s infinite ease;
	  	animation:animball_one 1s infinite ease;
	}
	.ball_1
	{
	  	background-color:#5587d7;
	  	top:0; left:0;
	}
	.ball_2
	{
	  	background-color:#db6e6e;
	  	top:0; left:24px;
	}
	.ball_3
	{
	  	background-color:#42b983;
	  	top:24px; left:0;
	}
	.ball_4
	{
	  	background-color:#edb431;
	  	top:24px; left:24px;
	}

	@-webkit-keyframes animball_one
	{
	  	0%{ position: absolute;}
	  	50%{top:12px; left:12px; position: absolute;opacity:0.5;}
	  	100%{ position: absolute;}
	}
	@-moz-keyframes animball_one
	{
	  	0%{ position: absolute;}
	  	50%{top:12px; left:12px; position: absolute;opacity:0.5;}
	  	100%{ position: absolute;}
	}
	@-ms-keyframes animball_one
	{
	  	0%{ position: absolute;}
	  	50%{top:12px; left:12px; position: absolute;opacity:0.5;}
	  	100%{ position: absolute;}
	}
	@keyframes animball_one
	{
	  	0%{ position: absolute;}
	  	50%{top:12px; left:12px; position: absolute;opacity:0.5;}
	  	100%{ position: absolute;}
	}

	@-webkit-keyframes animball_two
	{
	  	0%{-webkit-transform:rotate(0deg) scale(1);}
	  	50%{-webkit-transform:rotate(360deg) scale(1.3);}
	  	100%{-webkit-transform:rotate(720deg) scale(1);}
	}
	@-moz-keyframes animball_two
	{
	  0%{-moz-transform:rotate(0deg) scale(1);}
	  50%{-moz-transform:rotate(360deg) scale(1.3);}
	  100%{-moz-transform:rotate(720deg) scale(1);}
	}
	@-ms-keyframes animball_two
	{
	  0%{-ms-transform:rotate(0deg) scale(1);}
	  50%{-ms-transform:rotate(360deg) scale(1.3);}
	  100%{-ms-transform:rotate(720deg) scale(1);}
	}
	@keyframes animball_two
	{
	  0%{transform:rotate(0deg) scale(1);}
	  50%{transform:rotate(360deg) scale(1.3);}
	  100%{transform:rotate(720deg) scale(1);}
	}
</style>