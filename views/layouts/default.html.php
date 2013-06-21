<html>
<head>
	<title>{:block "title"}Hello World{block:}</title>
</head>
<body>
	<div class="navigation">
		{:block "login"}Login Screen here{block:}
	</div>
	<div class="content">
		{:block "body"}{block:}
	</div>
</body>
</html>