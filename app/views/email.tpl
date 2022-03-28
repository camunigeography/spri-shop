<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
	<head>
		<title>SPRI shop order</title>
		<!-- https://www.campaignmonitor.com/css/style-element/style-in-head/ -->
		<!-- https://www.campaignmonitor.com/css/style-element/style-in-head/ -->
		
		<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" />
		
		<style type="text/css">
		{literal}
			body {background-color: #e7f6fb; background-image: url('https://www.spri.cam.ac.uk/centenary/background.jpg'); background-repeat: no-repeat;}
			body {font-family: 'Open Sans', verdana, arial, sans-serif; font-size: 0.9em;}
			#content {width: 80%; margin: 10px auto; background-color: #fdfeff; padding: 10px 30px;}
			h2 {margin-top: 2em;}
			table.lines {border-collapse: collapse;}
			.lines td, .lines th {border-bottom: 1px solid #e9e9e9; padding: 6px 4px 2px; vertical-align: top; text-align: left;}
			.lines tr:first-child {border-top: 1px solid #e9e9e9;}
			.lines td h3 {text-align: left; padding-top: 20px;}
			.lines p {text-align: left;}
			.lines td.noline {border-bottom: 0;}
			table.lines td.value p:first-child {margin-top: 0;}
			table.lines td.value p:last-child {margin-bottom: 0;}
			table.lines td:last-child ul:first-child {margin-top: 0;}
			table.lines td:last-child ul:first-child li:first-child {margin-top: 0;}
			table th.background, table td.background {background-color: #eee;}
			table th.border, table td.border {border: 1px solid gray;}
		{/literal}
		</style>
	</head>
	<body style="background-color: #e7f6fb;">
		<div id="content">
			
			
			
			<h2>Your order from the SPRI Shop</h2>
			<p><strong>Thank you for your order.</strong></p>
			<p>You will separately receive a payment confirmation from our secure payment merchant.</p>
			<p>Details of the order are as follows. Please <a href="https://www.spri.cam.ac.uk/contacts/">contact us</a> if you have any questions.</p>
			
			<h2>Order summary</h2>
			
			<p>Order no.: <strong>{$id}</strong>.</p>
			<p>Total paid: <strong>&pound;{$amount}</strong> (+ <strong>&pound;{$postage}</strong> postage)</p>
			<p>Expected delivery date: <strong>{$deliveryDate}</strong></p>
			
			<table class="lines">
				<tr>
					<th class="image"></th>
					<th class="item">Item</th>
					<th class="price">Price</th>
					<th class="number">Number</th>
				</tr>
				{foreach from=$order item=item}
				<tr>
					<td class="image key"><a href="{$siteUrl}{$item.url}"><img src="{$siteUrl}{$item.image}" alt="{$item.name|htmlspecialchars}" align="right" width="100" border="0" /></a></td>
					<td class="item">{$item.name|htmlspecialchars}</td>
					<td class="price">&pound;{$item.price}</td>
					<td class="number">{$item.total}</td>
				</tr>
				{/foreach}
			</table>
			
			
			<h2>Your delivery details</h2>
			
			<p>Name:<br />
			{$forename|htmlspecialchars} {$surname|htmlspecialchars}</p>
			
			<p>Address:<br />
			{$address|htmlspecialchars|nl2br}</p>
			
			<p>Postcode:<br />
			{$postcode|htmlspecialchars}</p>
			
			<p>E-mail address:<br />
			{$email|htmlspecialchars}</p>
			
			<p>Telephone:<br />
			{$telephone|htmlspecialchars}</p>
			
			
		</div>
	</body>
</html>
