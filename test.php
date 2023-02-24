<!DOCTYPE HTML>
<html>
	<head>
		<title>HPC FTE PLANNING</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<link rel="stylesheet" href="assets/css/auto.css" />
	</head>
	<body class="is-preload">
        <!-- Wrapper -->
	<div id="wrapper">
    	    <!-- Main -->
    	    <div id="main">
		<div class="inner">

                <!-- Header -->
               	<header id="header">
               		<a href="index.php" class="logo"><strong>HPC</strong> FTE PLANNING</a>
           	</header>

<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/bootstrap-theme.min.css">
<script src="script_dir/jquery.min.js"></script>
<!--
-->
<script src="script_dir/popper.min.js"></script>
<script src="script_dir/bootstrap.min.js"></script>

<script src="script_dir/tableExport.js"></script>
<script type="text/javascript" src="script_dir/jquery.base64.js"></script>
<script src="script_dir/export.js"></script>
<script src="script_dir/bootstrap-select.min.js"></script>
<br><strong>Search</strong> Workpackages<br><br>

<!--
<table style='border:1px solid black;'>
<tr bgcolor ='#C1C1E8'>
<td align='left' class='btn-group pull-left'><button type='button' btn-lg dropdown-toggle' data-toggle='dropdown'>Export <span class='caret'></span></button>
<ul class='dropdown-menu' role='menu'>
<li><a class='dataExport' data-type='csv'>CSV</a></li><li><a class='dataExport' data-type='excel'>XLS</a></li><li><a class='dataExport' data-type='txt'>TXT</a></li></ul></td><td valign='top'><b>View by Year</b></td>
<td width='200'><select  onchange='refreshPage(this.value);' name='year[]' id='year' data-size='4' required='required' onchange='change()'><option value=''>Select</option><option value=2023 selected='true'>2023</option><option value=2024>2024</option></select></td></tr>
</table>

<input type='hidden' name='currentYear' value='2023'></form><script>
function refreshPage(passValue,search){
//do something in this function with the value
 window.location="searchworkpackageinfo.php?currentYear="+passValue
}
</script>

  <div class='container'><div class='row mt-4'><div class='col-md-8 mx-auto bg-light rounded p-4'><form action='' method='post' class='p-3'>

<table>




<tr><td><h5 class='text-center text-secondary'>Enter workpackage in the search box</h5></td></tr><tr><td><input type='text' name='search' id='search' -control-lg rounded-0 border-info' placeholder='Search...' autocomplete='off' required></td></tr><tr><td align='right'><input type='submit' name='submit' value='Search' class='btn btn-info btn-lg rounded-0'></td></tr></table>

</form></div><div class='col-md-5' style='position: relative;margin-top: -153px;margin-left: 205px;'><div class='list-group' id='show-list'></div></div></div></div>

  <script src="script_dir/script_workpackage_info.js"></script>

<table id='dataTable' class='table table-striped' width='100%'>
-->


<table id='dataTable'  width='100%'>
<!--
<tr bgcolor='#ADD8E6'>
<td colspan='100%'><b>ZH27 51943200 2023 Forcast </b></td>
</tr>

<tr bgcolor ='#C1C1E8'>
<td width='100%'  valign='top'><b>wp_id</b></td>
<td width='100%'  valign='top'><b>Program</b></td>
<td width='100%'  valign='top'><b>Project</b></td>
<td width='100%'  valign='top'><b>Task</b></td>
<td width='100%'  valign='top'><b>Task Name</b></td>
<td width='100%'  valign='top'><b>Task Manager</b></td>
<td width='100%'  valign='top'><b>Task Description</b></td>
<td width='100%'  valign='top'><b>Burden Rate</b></td>
<td width='100%'  valign='top'><b>Start Date</b></td>
<td width='100%'  valign='top'><b>End Date</b></td>
</tr>

<tr bgcolor='#E3E3E3'>
<td>5</td>
<td>FOUS</td>
<td>ZH27</td>
<td>51943200</td>
<td>SCC Cooling Tower Agitators - Subcontract Construction</td>
<td>Reggie Page</td>
<td>Install Agitators in the SCC Cooling Towers</td>
<td>1</td>
<td>2022-10-01</td>
<td>2023-09-30</td>
</tr>

<tr>
<td colspan='100%'></td>
</tr>

<tr bgcolor='#ADD8E6'>
<td colspan='100%'><b>Retained Team</b></td>
</tr>

<tr bgcolor ='#C1C1E8'>
<td>&nbsp;</td>
</tr>

<tr>
<td colspan='100%'></td>
</tr>

<tr bgcolor='#ADD8E6'>
<td colspan='100%'><b>Service & Support Contracts</b></td>
</tr>

<tr bgcolor ='#C1C1E8'>
<td width='100%'  valign='top'><b>Service</b></td>
<td width='100%'  valign='top'><b>Start Date</b></td>
<td width='100%'  valign='top'><b>End Date</b></td>
<td width='100%'  valign='top'><b>Owner</b></td>
<td width='100%'  valign='top'><b>Vendor</b></td>
<td width='100%'  valign='top'><b>Percent of Fous</b></td>
<td width='100%'  valign='top'><b>Risk</b></td>
<td width='100%'  valign='top'><b>Funded</b></td>
<td width='100%'  valign='top'><b>Cost</b></td>
<td width='100%'  valign='top'><b>Total Cost</b></td>
<td width='100%'  valign='top'><b>Notes</b></td>
</tr>

<tr bgcolor='#E3E3E3'>
<td>Subcontract Installation</td>
<td>2022-10-01</td>
<td>2022-12-31</td>
<td>Reggie Page</td>
<td>Pueblo Alliance</td>
<td>100%</td>
<td>0</td>
<td>Yes</td>
<td>$977,239.00</td>
<td>$977,239.00</td>
<td>&nbsp;</td>
</tr>
-->
<tr bgcolor='red'>
<td><b>Totals:</b></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td><b>$977,239</b></td>
<td><b>$977,239.00</b></td>
<td>&nbsp;</td>
</tr>
<!--
<tr>
<td colspan='100%'></td>
</tr>

<tr bgcolor='#ADD8E6'>
<td colspan='100%'><b>Systems & Materials</b></td>
</tr>

<tr bgcolor ='#C1C1E8'>
<td>&nbsp;</td>
</tr>

<tr>
<td colspan='100%'></td>
</tr>

<tr bgcolor='#ADD8E6'>
<td colspan='100%'><b>Activity Descriptions</b></td>
</tr>

<tr bgcolor ='#C1C1E8'>
<td>&nbsp;</td>
</tr>

<tr>
<td colspan='100%'></td>
</tr>

<tr bgcolor='#ADD8E6'>
<td colspan='100%'><b>Workpackage Info</b></td>
</tr>

<tr bgcolor ='#C1C1E8'>
<td width='100%'  valign='top'><b>Burden Rate</b></td>
<td width='100%'  valign='top'><b>Request FTE</b></td>
<td width='100%'  valign='top'><b>Funded FTE</b></td>
<td width='100%'  valign='top'><b>Serivce Costs</b></td>
<td width='100%'  valign='top'><b>Hardware Inventory</b></td>
<td width='100%'  valign='top'><b>Hardware Costs</b></td>
<td width='100%'  valign='top'><b>FY ALLOCTIONS</b></td>
</tr>

<tr bgcolor='#E3E3E3'>
<td>1</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>$977,239</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>

<tr>
<td colspan='100%'></td>
</tr>
-->

</table>

                </div>
        </div>

<!-- Sidebar -->

	<div id="sidebar">
		<div class="inner">

			<!-- Search
				<section id="search" class="alt">
					<form method="post" action="#">
						<input type="text" name="query" id="query" placeholder="Search" />
					</form>
				</section>
			 -->

			<!-- Menu -->
<nav id="menu">
	<header class="major">
		<h2>Menu</h2>
	</header>
	<ul>
		<li><a href="index.php">Homepage</a></li>
		<li>
			<span class="opener">Staff</span>
			<ul>
		                <li><a href="Staff.php">Staff</a></li>
		                <li><a href="update_add_staff.php">Update/Add Staff</a></li>
			</ul>
		</li>

		<li>
			<span class="opener">FTE Plannning</span>
			<ul>
                                <!--
		                <li><a href="allftesSYS.php">View SYS FTE's</a></li>
		                <li><a href="allftesENV.php">View ENV FTE's</a></li>
                                -->
		                <li><a href="allftes.php">View Group FTE's</a></li>
		                <li><a href="searchstaff.php">Search By Staff</a></li>
		                <li><a href="searchworkpackage.php">Search By Workpackage</a></li>
		                <li><a href="searchteam.php">Search By Team</a></li>
		                <li><a href="update_staff_fte.php">Update FTE</a></li>
			</ul>
		</li>
		<li>
			<span class="opener">Workpackages</span>
			<ul>
		                <li><a href="workpackage_managers.php">Workpackage Managers</a></li>
		                <li><a href="searchworkpackageinfo.php">Search Workpackage Info</a></li>
		                <li><a href="update_workpackageinfo.php">Update WP Info</a></li>
		                <li><a href="update_workpackage_staff.php">Update/Add WP Staff</a></li>
		                <li><a href="update_workpackage_activities.php">Update/Add WP Activities</a></li>
		                <li><a href="update_workpackage_services.php">Update/Add Services </a></li>
		                <li><a href="update_workpackage_materials.php">Update/Add Materials</a></li>
			</ul>
		</li>
		<li><a href="teams.php">Teams</a></li>
	</ul>
</nav>


<section>
      <header class="major">
              <h2> Under Or Over</h2>
      </header>
       <div class="mini-posts">
               <article>
                       <font size='1'>
<table class='style1' width='300' style='border:1px solid black;'>
<tr bgcolor ='#C1C1E8'>
<td valign='top'><b>Workpackage</b></td>
<td valign='top'><b>Forcasted</b></td>
<td valign='top'><b>Allocated</b></td>
<td valign='top'><b>Difference</b></td>
</tr>
<tr bgcolor='#E3E3E3'>
<td valign='top'><font color='black'>ZG67 3199 0013</font></td>
<td valign='top' align='right'><font color='black'>0</font></td>
<td valign='top'align='right'><font color='black'>2.00</font></td>
<td valign='top'align='right'><font color='red'>-2.00</font></td>
</tr>
<tr bgcolor='#FFFFFF'>
<td valign='top'><font color='black'>X5D1 0004 TTEC (off replacement for JAF2)</font></td>
<td valign='top' align='right'><font color='black'>0</font></td>
<td valign='top'align='right'><font color='black'>1.05</font></td>
<td valign='top'align='right'><font color='red'>-1.05</font></td>
</tr>
<tr bgcolor='#E3E3E3'>
<td valign='top'><font color='black'>XAD1 22CYBER0 (ICCI)</font></td>
<td valign='top' align='right'><font color='black'>0</font></td>
<td valign='top'align='right'><font color='black'>0.30</font></td>
<td valign='top'align='right'><font color='red'>-0.30</font></td>
</tr>
<tr bgcolor='#FFFFFF'>
<td valign='top'><font color='black'>JA7K</font></td>
<td valign='top' align='right'><font color='black'>0</font></td>
<td valign='top'align='right'><font color='black'>0.25</font></td>
<td valign='top'align='right'><font color='red'>-0.25</font></td>
</tr>
<tr bgcolor='#E3E3E3'>
<td valign='top'><font color='black'>ZF45 0000 (SAGE)</font></td>
<td valign='top' align='right'><font color='black'>0</font></td>
<td valign='top'align='right'><font color='black'>0.20</font></td>
<td valign='top'align='right'><font color='red'>-0.20</font></td>
</tr>
<tr bgcolor='#FFFFFF'>
<td valign='top'><font color='black'>XPGS 0013 SAGE</font></td>
<td valign='top' align='right'><font color='black'>0.45</font></td>
<td valign='top'align='right'><font color='black'>0.25</font></td>
<td valign='top'align='right'><font color='black'>+0.20</font></td>
</tr>
<tr bgcolor='#E3E3E3'>
<td valign='top'><font color='black'>X36P UNCL 1100</font></td>
<td valign='top' align='right'><font color='black'>3.03</font></td>
<td valign='top'align='right'><font color='black'>2.83</font></td>
<td valign='top'align='right'><font color='black'>+0.20</font></td>
</tr>
<tr bgcolor='#FFFFFF'>
<td valign='top'><font color='black'>JAF2 NETW</font></td>
<td valign='top' align='right'><font color='black'>2.65</font></td>
<td valign='top'align='right'><font color='black'>2.40</font></td>
<td valign='top'align='right'><font color='black'>+0.25</font></td>
</tr>
<tr bgcolor='#E3E3E3'>
<td valign='top'><font color='black'>JAF2 FILE</font></td>
<td valign='top' align='right'><font color='black'>1.65</font></td>
<td valign='top'align='right'><font color='black'>1.40</font></td>
<td valign='top'align='right'><font color='black'>+0.25</font></td>
</tr>
<tr bgcolor='#FFFFFF'>
<td valign='top'><font color='black'>X5VG DATA CAMP</font></td>
<td valign='top' align='right'><font color='black'>1.05</font></td>
<td valign='top'align='right'><font color='black'>0.75</font></td>
<td valign='top'align='right'><font color='black'>+0.30</font></td>
</tr>
<tr bgcolor='#E3E3E3'>
<td valign='top'><font color='black'>JAGL 00000000 (asc-git)</font></td>
<td valign='top' align='right'><font color='black'>1.2</font></td>
<td valign='top'align='right'><font color='black'>0.70</font></td>
<td valign='top'align='right'><font color='black'>+0.50</font></td>
</tr>
<tr bgcolor='#FFFFFF'>
<td valign='top'><font color='black'>JAF4 ARCH</font></td>
<td valign='top' align='right'><font color='black'>1.85</font></td>
<td valign='top'align='right'><font color='black'>1.05</font></td>
<td valign='top'align='right'><font color='black'>+0.80</font></td>
</tr>
<tr bgcolor='#E3E3E3'>
<td valign='top'><font color='black'>JAJC Consult</font></td>
<td valign='top' align='right'><font color='black'>2</font></td>
<td valign='top'align='right'><font color='black'>1.10</font></td>
<td valign='top'align='right'><font color='black'>+0.90</font></td>
</tr>
<tr bgcolor='#FFFFFF'>
<td valign='top'><font color='black'>JAF2 CAMP</font></td>
<td valign='top' align='right'><font color='black'>1.95</font></td>
<td valign='top'align='right'><font color='black'>0.75</font></td>
<td valign='top'align='right'><font color='black'>+1.20</font></td>
</tr>
<tr bgcolor='#E3E3E3'>
<td valign='top'><font color='black'>X5VG Test</font></td>
<td valign='top' align='right'><font color='black'>2.2</font></td>
<td valign='top'align='right'><font color='black'>0.50</font></td>
<td valign='top'align='right'><font color='black'>+1.70</font></td>
</tr>
<tr bgcolor='#FFFFFF'>
<td valign='top'><font color='black'>JAJC WLM</font></td>
<td valign='top' align='right'><font color='black'>2.8</font></td>
<td valign='top'align='right'><font color='black'>1.04</font></td>
<td valign='top'align='right'><font color='black'>+1.76</font></td>
</tr>
<tr bgcolor='#E3E3E3'>
<td valign='top'><font color='black'>JALD CSP0</font></td>
<td valign='top' align='right'><font color='black'>2.9</font></td>
<td valign='top'align='right'><font color='black'>0.90</font></td>
<td valign='top'align='right'><font color='black'>+2.00</font></td>
</tr>
<tr bgcolor='#FFFFFF'>
<td valign='top'><font color='black'>JAJC PROD</font></td>
<td valign='top' align='right'><font color='black'>7.8</font></td>
<td valign='top'align='right'><font color='black'>5.35</font></td>
<td valign='top'align='right'><font color='black'>+2.45</font></td>
</tr>
<tr bgcolor='#E3E3E3'>
<td valign='top'><font color='black'>X5VG WLM</font></td>
<td valign='top' align='right'><font color='black'>4</font></td>
<td valign='top'align='right'><font color='black'>1.50</font></td>
<td valign='top'align='right'><font color='black'>+2.50</font></td>
</tr>
<tr bgcolor='#FFFFFF'>
<td valign='top'><font color='black'>JALD TECH OPS0</font></td>
<td valign='top' align='right'><font color='black'>12.78</font></td>
<td valign='top'align='right'><font color='black'>9.62</font></td>
<td valign='top'align='right'><font color='black'>+3.16</font></td>
</tr>
<tr bgcolor='#E3E3E3'>
<td valign='top'><font color='black'>JAJC Test</font></td>
<td valign='top' align='right'><font color='black'>5.2</font></td>
<td valign='top'align='right'><font color='black'>0.50</font></td>
<td valign='top'align='right'><font color='black'>+4.70</font></td>
</tr>
</table>
</font>
               </article>
       </div>
       <ul class="actions">
               <li><a href="overunder.php" class="button">More</a></li>
       </ul>
</section>






<!--
								<section>
									<header class="major">
										<h2> SOME HEADER</h2>
									</header>
									<div class="mini-posts">
										<article>
											<a href="#" class="image"><img src="images/pic07.jpg" alt="" /></a>
											<p>Aenean ornare velit lacus, ac varius enim lorem ullamcorper dolore aliquam.</p>
										</article>
										<article>
											<a href="#" class="image"><img src="images/pic08.jpg" alt="" /></a>
											<p>Aenean ornare velit lacus, ac varius enim lorem ullamcorper dolore aliquam.</p>
										</article>
										<article>
											<a href="#" class="image"><img src="images/pic09.jpg" alt="" /></a>
											<p>Aenean ornare velit lacus, ac varius enim lorem ullamcorper dolore aliquam.</p>
										</article>
									</div>
									<ul class="actions">
										<li><a href="#" class="button">More</a></li>
									</ul>
								</section>


								<section>
									<header class="major">
										<h2>Get in touch</h2>
									</header>
									<p>Sed varius enim lorem ullamcorper dolore aliquam aenean ornare velit lacus, ac varius enim lorem ullamcorper dolore. Proin sed aliquam facilisis ante interdum. Sed nulla amet lorem feugiat tempus aliquam.</p>
									<ul class="contact">
										<li class="icon solid fa-envelope"><a href="#">information@untitled.tld</a></li>
										<li class="icon solid fa-phone">(000) 000-0000</li>
										<li class="icon solid fa-home">1234 Somewhere Road #8254<br />
										Nashville, TN 00000-0000</li>
									</ul>
								</section>
-->

							<!-- Footer -->
								<footer id="footer">
								</footer>

						</div>
					</div>

			</div>

		<!-- Scripts -->
	<!--		<script src="assets/js/jquery.min.js"></script> -->
			<script src="assets/js/browser.min.js"></script>
			<script src="assets/js/breakpoints.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>

	</body>
</html>

