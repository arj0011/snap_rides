@extends('admin.layouts.app')
@section('title', 'Profile')
@section('content')   
     <!-- Page Content -->
 <div id="page-wrapper">

<div id="content">
	<div class="inner">
		<div class="row">
			<div class="col-lg-12">
				<h2>Add Driver  </h2>
				<a href="javascript:void(0);" class="back_link">
					<input type="button" value="Back to Listing" class="add-btn">
				</a>
			</div>
		</div>
		<hr />
		<div class="body-div">
			<div class="form-group">
				
				<form id="_driver_form" name="_driver_form" method="post" action="" enctype="multipart/form-data">
					<input type="hidden" name="actionOf" id="actionOf" value="Add"/>
					<input type="hidden" name="id" id="iDriverId" value=""/>
					<input type="hidden" name="oldImage" value=""/>
					<input type="hidden" name="previousLink" id="previousLink" value=""/>
					<input type="hidden" name="backlink" id="backlink" value="driver.php"/>

					<div class="row">
						<div class="col-lg-12">
							<label>First Name<span class="red"> *</span></label>
						</div>
						<div class="col-lg-6">
							<input type="text" class="form-control" name="vName"  id="vName" value="" placeholder="First Name" >
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<label>Last Name<span class="red"> *</span></label>
						</div>
						<div class="col-lg-6">
							<input type="text" class="form-control" name="vLastName"  id="vLastName" value="" placeholder="Last Name" >
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<label>Email<span class="red"> *</span></label>
						</div>
						<div class="col-lg-6">
							<input type="text" class="form-control" name="vEmail"  id="vEmail" value="" placeholder="Email" >
						</div><div id="emailCheck"></div>
					</div>
					<div class="row">
						<div class="col-lg-12">
							<label>Password <span class="red"> *</span></label>
						</div>
						<div class="col-lg-6">
							<input type="password" class="form-control" name="vPassword"  id="vPassword" value="" placeholder="Password" >
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<label>Gender</label>
						</div>
						<div class="col-lg-6 ">
							<input id="r4" name="eGender" type="radio" value="Male"
							>
							<label for="r4">Male</label>&nbsp;&nbsp;&nbsp;&nbsp;
							<input id="r5" name="eGender" type="radio" value="Female" class="required" 
							>
							<label for="r5">Female</label>
						</div>
					</div>
 

					<div class="row">
						<div class="col-lg-12">
							<label>Profile Picture</label>
						</div>
						<div class="col-lg-6">
							<input type="file" class="form-control" name="vImage"  id="vImage" placeholder="Name Label" style="padding-bottom: 39px;">
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<label>Country <span class="red"> *</span></label>
						</div>
						<div class="col-lg-6">
							<select class="form-control" name = 'vCountry' id="vCountry" onChange="setState(this.value,''),changeCode(this.value);" >
								<option value="">Select</option></select>
							 
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<label>State</label>
						</div>
						<div class="col-lg-6">
							<select class="form-control" name = 'vState' id="vState" onChange="setCity(this.value,'');" >
								<option value="">Select</option>
							</select>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<label>City</label>
						</div>
						<div class="col-lg-6">
							<select class="form-control" name = 'vCity' id="vCity"  >
								<option value="">Select</option>
							</select>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<label>Address <span class="red"> *</span></label>
						</div>
						<div class="col-lg-6">
							<input type="text"  class="form-control" name="vCaddress"  id="vCaddress" value="" placeholder="Address" >
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<label>Zip Code<span class="red">*</span></label>
						</div>
						<div class="col-lg-6">
							<input type="text" class="form-control" name="vZip"  id="vZip" value="" placeholder="Zip Code" required>
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<label>Phone<span class="red"> *</span></label>
						</div>
						<div class="col-lg-6">
							<input type="text" class="form-select-2" id="code" name="vCode" value=""  readonly style="width: 10%;height: 36px;text-align: center;"/ >
							<input type="text" class="form-control"  style="margin-top: 5px; width:90%;" name="vPhone"  id="vPhone" value="" placeholder="Phone" >
						</div>
					</div>

					<div class="row">
						<div class="col-lg-12">
							<label>Company<span class="red"> *</span></label>
						</div>
						<div class="col-lg-6">

							<select  class="form-control" name = 'iCompanyId'  id= 'iCompanyId' >
								<option value="">--select--</option>
							 
								 
							</select>
						</div>
					</div>
					 

 
					<div class="row">
						<div class="col-lg-12">
							<input type="submit" class="btn btn-default" name="submit" id="submit" value="Add Driver" >
							<input type="reset" value="Reset" class="btn btn-default">
							<!-- <a href="javascript:void(0);" onClick="reset_form('_driver_form');" class="btn btn-default">Reset</a> -->
							<a href="driver.php" class="btn btn-default back_link">Cancel</a>
						</div>
					</div>
				</form>
        	</div>


   		</div>


   		
 	</div>       
</div>

</div>
  <!-- /#page-wrapper -->
@endsection