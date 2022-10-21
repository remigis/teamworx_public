@extends('layouts.app') @section('content')

<section id="banner2" class="jumbotron text-center inner_shadow">
	<div class="container fun_msg">
		<h1 class="jumbotron-heading">Album example</h1>
		<p class="lead text-white">Something short and leading about the
			collection below—its contents, the creator, etc. Make it short and
			sweet, but not too short so folks don't simply skip over it entirely.</p>
	</div>
</section>

<div id="about_us" class="container">
	<hr>
	<div class="row justify-content-center">
		<div class="col-md-10">
			<div class="pricing-header p-3 pb-md-4 mx-auto text-center">
				<h1 class="fw-normal">About Us</h1>
				<p class="fs-5 text-muted">We are a subsidiary of bit-electronix.eu
					- a long-time leading merchant of refurbished goods across many
					online platforms. Our main focus is goods refurbishment. We inspect
					for errors and, if needed, repair electronics of well-known high
					quality brands.</p>
			</div>


			<div class="row row-cols-1 row-cols-md-3 my-5 text-center">
				<div class="col d-flex align-items-stretch">
					<div class="card mb-4 rounded-3 shadow-sm">
						<div class="card-header py-3">
							<h4 class="my-0 fw-normal">Company</h4>
						</div>
						<div class="card-body">
							<h1 class="card-title pricing-card-title">
								<span class="my_color"><i class="fas fa-building"></i></span>
							</h1>
							<p>The company was established in late 2013 as a subsidiary in
								effort to expand the operations of Bit-Electronix - a leading
								online retailer for professional gaming equipment in Germany.</p>
                        </div>
                    </div>
                </div>
                <div class="col d-flex align-items-stretch">
                    <div class="card mb-4 rounded-3 shadow-sm zoom_mid">
                        <div class="card-header py-3">
                            <h4 class="my-0 fw-normal">Leader in Refurbishment</h4>
                        </div>
                        <div class="card-body">
                            <h1 class="card-title pricing-card-title">
                                <span class="my_color"><i class="fa-solid fa-award"></i></span>
                            </h1>
                            <p>Our main focus is Refurbishment of returned items. We are one
                                of the biggest refurbishment companies in Lithuania, processing
                                hundreds of items of most popular brands daily, including
                                Logitech, Asus and Razer.</p>
                        </div>
                    </div>
                </div>
                <div class="col d-flex align-items-stretch">
                    <div class="card mb-4 rounded-3 shadow-sm">
						<div class="card-header py-3">
							<h4 class="my-0 fw-normal">Teamwork is Key</h4>
						</div>
						<div class="card-body">
							<h1 class="card-title pricing-card-title">
								<span class="my_color"><i class="fas fa-users"></i></span>
							</h1>
							<p>We build a professional, yet friendly and flexible working
								environment. We believe this is critical in allowing for
								efficient teams to be formed, as teamwork is critical in process
								of refurbishment.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<hr>
</div>

<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-10">
			<div class="pricing-header p-3 pb-md-4 mx-auto text-center">
				<h2 class="fw-normal">Brands We Work With</h2>
				<p>We are very proud to be working with some of the worlds leading
					brands.</p>
				<div class="logos">
					<div class="slide">
						<img
							src="https://raw.githubusercontent.com/solodev/infinite-carousel/master/images/image1.png">
					</div>
					<div class="slide">
						<img
							src="https://raw.githubusercontent.com/solodev/infinite-carousel/master/images/image2.png">
					</div>
					<div class="slide">
						<img
							src="https://raw.githubusercontent.com/solodev/infinite-carousel/master/images/image3.png">
					</div>
					<div class="slide">
						<img
							src="https://raw.githubusercontent.com/solodev/infinite-carousel/master/images/image4.png">
					</div>
					<div class="slide">
						<img
							src="https://raw.githubusercontent.com/solodev/infinite-carousel/master/images/image5.png">
					</div>
					<div class="slide">
						<img
							src="https://raw.githubusercontent.com/solodev/infinite-carousel/master/images/image6.png">
					</div>
					<div class="slide">
						<img
							src="https://raw.githubusercontent.com/solodev/infinite-carousel/master/images/image7.png">
					</div>
					<div class="slide">
						<img
							src="https://raw.githubusercontent.com/solodev/infinite-carousel/master/images/image8.png">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="contacts" class="container">
	<hr>
	<div class="row justify-content-center">
		<div class="col-md-10">
			<div class="pricing-header p-3 pb-md-4 mx-auto text-center">
				<h1 class="fw-normal">Contacts</h1>
			</div>

			<div class="col-md-12">
				<div class="row">
					<div class="col-md-6 d-flex align-items-stretch">
						<div style="width: 100%">
							<iframe
								src="https://maps.google.com/maps?width=100%25&height=400&hl=en&q=Zalgirio+g.+108%2C+Vilnius%2C+09300&t=&z=14&ie=UTF8&iwloc=B&output=embed"
								width="100%" height="300"></iframe>
						</div>
					</div>
					<div class="col-md-6 d-flex align-items-stretch">

						<ul class="list-group col-md-12">
							<li class="list-group-item"><i class="fas fa-phone-alt"></i>
								Phone: +37061759956</li>
							<li class="list-group-item"><i class="fas fa-at"></i> E-Mail:
								info@teamworx.site</li>
							<li class="list-group-item"><i class="fas fa-map-marked-alt"></i>
								Address: Žalgirio g. 108, Vilnius, LT-09300</li>
							<li class="list-group-item flex-column align-items-start">
								<div class="d-flex w-100 justify-content-between">
									<h5 class="mb-1"><i class="fas fa-calendar-alt"></i> Working hours</h5>
								</div>
								<table>
								<tr>
								<th>I-IV:</th>
								<td>09:00-18:00</td>
								</tr>
								<tr>
								<th>V:</th>
								<td>09:00-16:45</td>
								</tr>
								<tr>
								<th>VI-VII:</th>
								<td>Not working</td>
								</tr>
								</table>
								<small>Before the holidays and during the holidays working hours may differ.</small>
							</li>



						</ul>


					</div>
				</div>
			</div>



		</div>
	</div>
	<hr>
</div>

<div id="contacts" class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="pricing-header p-3 pb-md-4 mx-auto text-center">
                <h2 class="fw-normal">Feel free to send us a message</h2>
            </div>

            <div class="col-md-12">

                <form class='col-md-12' action="{{route('sendContactUsMessage')}}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email address</label> <input type="email"
                                                                        class="form-control" id="email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="text">Message</label>
                        <textarea id="text" name="text" class="form-control"
                                  rows="5"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send
                    </button>
                </form>


            </div>



		</div>
	</div>
	<hr>
</div>

<!-- Footer -->
<footer class="text-center text-lg-start bg-light text-muted">
	<!-- Section: Social media -->
	<section
		class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
		<!-- Left -->
		<div class="me-5 d-none d-lg-block">
			<span>Get connected with us on social networks:</span>
		</div>
		<!-- Left -->

		<!-- Right -->
		<div>
			<a href="" class="me-4 text-reset"> <i class="fab fa-facebook-f"></i>
			</a> <a href="" class="me-4 text-reset"> <i class="fab fa-twitter"></i>
			</a> <a href="" class="me-4 text-reset"> <i class="fab fa-google"></i>
			</a> <a href="" class="me-4 text-reset"> <i class="fab fa-instagram"></i>
			</a> <a href="" class="me-4 text-reset"> <i class="fab fa-linkedin"></i>
			</a> <a href="" class="me-4 text-reset"> <i class="fab fa-github"></i>
			</a>
		</div>
		<!-- Right -->
	</section>
	<!-- Section: Social media -->

	<!-- Section: Links  -->
	<section class="">
		<div class="container text-center text-md-start mt-5">
			<!-- Grid row -->
			<div class="row mt-3">
				<!-- Grid column -->
				<div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
					<!-- Content -->
					<h6 class="text-uppercase fw-bold mb-4">
						<i class="fas fa-gem me-3"></i>Company name
					</h6>
					<p>Here you can use rows and columns to organize your footer
						content. Lorem ipsum dolor sit amet, consectetur adipisicing elit.
					</p>
				</div>
				<!-- Grid column -->

				<!-- Grid column -->
				<div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
					<!-- Links -->
					<h6 class="text-uppercase fw-bold mb-4">Products</h6>
					<p>
						<a href="#!" class="text-reset">Angular</a>
					</p>
					<p>
						<a href="#!" class="text-reset">React</a>
					</p>
					<p>
						<a href="#!" class="text-reset">Vue</a>
					</p>
					<p>
						<a href="#!" class="text-reset">Laravel</a>
					</p>
				</div>
				<!-- Grid column -->

				<!-- Grid column -->
				<div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
					<!-- Links -->
					<h6 class="text-uppercase fw-bold mb-4">Useful links</h6>
					<p>
						<a href="#!" class="text-reset">Pricing</a>
					</p>
					<p>
						<a href="#!" class="text-reset">Settings</a>
					</p>
					<p>
						<a href="#!" class="text-reset">Orders</a>
					</p>
					<p>
						<a href="#!" class="text-reset">Help</a>
					</p>
				</div>
				<!-- Grid column -->

				<!-- Grid column -->
				<div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
					<!-- Links -->
					<h6 class="text-uppercase fw-bold mb-4">Contact</h6>
					<p>
						<i class="fas fa-home me-3"></i> New York, NY 10012, US
					</p>
					<p>
						<i class="fas fa-envelope me-3"></i> info@example.com
					</p>
					<p>
						<i class="fas fa-phone me-3"></i> + 01 234 567 88
					</p>
					<p>
						<i class="fas fa-print me-3"></i> + 01 234 567 89
					</p>
				</div>
				<!-- Grid column -->
			</div>
			<!-- Grid row -->
		</div>
	</section>
	<!-- Section: Links  -->

	<!-- Copyright -->
	<div class="text-center p-4"
		style="background-color: rgba(0, 0, 0, 0.05);">
		© 2021 Copyright: <a class="text-reset fw-bold"
			href="https://mdbootstrap.com/">Remigijus Šimkus</a>
	</div>
	<!-- Copyright -->
</footer>
<!-- Footer -->
@endsection
