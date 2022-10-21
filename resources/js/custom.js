$('.navbar-nav li a').on('click', function() {
	if (!$(this).hasClass('dropdown-toggle')) {
		$('.navbar-collapse').collapse('hide');
	}
});

$('.logos').slick({
	slidesToShow: 4,
	slidesToScroll: 1,
	autoplay: true,
	autoplaySpeed: 1000,
	arrows: true,
	mobileFirst: true,
	prevArrow: '<button type="button" data-role="none" class="slick-prev slick-arrow" aria-label="Previous" role="button" style="display: block;"><i class="bi bi-arrow-left-circle-fill"></i></button>',
	nextArrow: '<button type="button" data-role="none" class="slick-next slick-arrow" aria-label="Next" role="button" style="display: block;"><i class="bi bi-arrow-right-circle-fill"></i></button>',
	responsive: [
		{
			breakpoint: 1120,
			settings: {
				slidesToShow: 4,
				slidesToScroll: 1
			}
		},
		{
			breakpoint: 960,
			settings: {
				slidesToShow: 3,
				slidesToScroll: 1
			}
		},
		{
			breakpoint: 450,
			settings: {
				slidesToShow: 2,
				slidesToScroll: 1
			}
		},
		{
			breakpoint: 0,
			settings: {
				slidesToShow: 1,
				slidesToScroll: 1
			}
		}
	],
});
