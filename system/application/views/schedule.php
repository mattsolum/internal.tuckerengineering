<section id="schedule">
	<div id="dates">
		<span id="date">July 24th-30th</span>
		<ul>
			<li>
				<a href="#">Sun, 24th</a>
			</li>
			<li>
				<a href="#">Mon, 25th</a>
			</li>
			<li>
				<a href="#">Tue, 26th</a>
			</li>
			<li>
				<a href="#">Wed, 27th</a>
			</li>
			<li>
				<a href="#" class="active">Thu, 28th</a>
			</li>
			<li>
				<a href="#">Fri, 29th</a>
			</li>
			<li>
				<a href="#">Sat, 30th</a>
			</li>
		</ul>	
	</div>
	
	<div id="daily">
		<ul class="inspectors">
			<li class="blue">
				Jeff Tucker
			</li>
			<li class="red">
				Kevin Tucker
			</li>
		</ul>
		<div id="scheduleContainer">
			<div id="scheduleDeviders">
				<ul id="times">
					<li>12 am</li>
					<li>1 am</li>
					<li>2 am</li>
					<li>3 am</li>
					<li>4 am</li>
					<li>5 am</li>
					<li>6 am</li>
					<li>7 am</li>
					<li>8 am</li>
					<li>9 am</li>
					<li>10 am</li>
					<li>11 am</li>
					<li>12 pm</li>
					<li>1 pm</li>
					<li>2 pm</li>
					<li>3 pm</li>
					<li>4 pm</li>
					<li>5 pm</li>
					<li>6 pm</li>
					<li>7 pm</li>
					<li>8 pm</li>
					<li>9 pm</li>
					<li>10 pm</li>
					<li>11 pm</li>
				</ul>
				<div id="schedules">
					<ul class="schedule jefftucker">
						<li class="green" style="margin-left:2014px">
							<a href="#jobs/{job_number}">
								1080 Bee Caves Rd, A<br />
								J. Smith 512 555-5555
							</a>
						</li>
					</ul>
					<ul class="schedule kevintucker">
						<li class="yellow" style="margin-left:1694px">
							<a href="#jobs/{job_number}">
								9801 W Parmer Ln<br />
								B. Barnett 512 555-5555
							</a>
						</li>
					</ul>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
</section>

<script type="text/javascript">
	
	$(document).ready(function() {
		var heightOfUIElements = $("#schedule").height() + $("#page_header").height() + $("#scheduleNew").height();
		
		$("#map").height($(window).height() - heightOfUIElements);
		
		$(window).resize(function () {
			$("#map").height($(window).height() - heightOfUIElements);
		});
	});
</script>

<div id="map">
	<div id="map_canvas"></div>
	<div id="shadow_top"></div>
	<div id="shadow_bottom"></div>
</div>

<section id="scheduleNew">
	<form id="schedule">
		<input type="text" name="address" id="schedule_new_address" value="" rel="Address">
		<select name="type" id="schedule_new_type" rel="Type">
			<option value="">Type</option>
			<option value="Inspection/Report">Inspection/Report</option>
			<option value="Certification">Certification</option>
			<option value="Design Site Visit">Design Site Visit</option>
			<option value="Consultation">Consultation</option>
		</select>
		<input type="submit" value="Add Job" />
	</form>
</section>