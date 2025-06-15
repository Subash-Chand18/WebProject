        <!-- Modal Search Start -->
        <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Search by keyword</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex align-items-center">
                        <div class="input-group w-75 mx-auto d-flex">
                            <input type="search" class="form-control p-3" placeholder="keywords" aria-describedby="search-icon-1">
                            <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Search End -->


        <!-- Hero Start -->
        <div class="container-fluid py-5 mb-5 hero-header">
            <style>
                /* Hover Effects */
                .carousel-item img {
                    transition: transform 0.5s ease, box-shadow 0.5s ease;
                }

                .carousel-item img:hover {
                    transform: scale(1.05);
                    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
                    z-index: 1;
                }

                .carousel-item a {
                    transition: background-color 0.3s ease, color 0.3s ease;
                }

                .carousel-item a:hover {
                    background-color: white !important;
                    color: #0d6efd !important; /* Bootstrap Primary color */
                }

                .btn-primary:hover {
                    background-color: #0b5ed7 !important;
                    border-color: #0a58ca !important;
                }
            </style>

            <div class="container py-5">
                <div class="row g-5 align-items-center">
                    <div class="col-md-12 col-lg-7">
                        <h4 class="mb-3 text-secondary">100% Best Suitable Clothes</h4>
                        <h1 class="mb-5 display-3 text-primary">Brand New Clothes</h1>
                        <div class="position-relative mx-auto">
                            <input class="form-control border-2 border-secondary w-75 py-3 px-4 rounded-pill" type="text" placeholder="Search">
                            <button type="submit" class="btn btn-primary border-2 border-secondary py-3 px-4 position-absolute rounded-pill text-white h-100" style="top: 0; right: 25%;">Submit Now</button>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-5">
                        <div id="carouselId" class="carousel slide position-relative" data-bs-ride="carousel">
                            <div class="carousel-inner" role="listbox">

                                <div class="carousel-item active rounded">
                                    <img src="assets/images/shoes2.jpg" class="img-fluid w-100 h-100 bg-secondary rounded" alt="First slide">
                                    <a href="#" class="btn btn-outline-light bg-primary px-4 py-2 text-white rounded">Men</a>
                                </div>

                                <div class="carousel-item rounded">
                                    <img src="assets/images/black gown.jpeg" class="img-fluid w-100 h-100 rounded" alt="Second slide">
                                    <a href="#" class="btn btn-outline-light bg-primary px-4 py-2 text-white rounded">Women</a>
                                </div>

                                <div class="carousel-item rounded">
                                    <img src="assets/images/classic white t-shirt.jpg" class="img-fluid w-100 h-100 bg-secondary rounded" alt="First slide">
                                    <a href="#" class="btn btn-outline-light bg-primary px-4 py-2 text-white rounded">Men</a>
                                </div>

                                <div class="carousel-item rounded">
                                    <img src="assets/images/babies combo set.jpg" class="img-fluid w-100 h-100 rounded" alt="Second slide">
                                    <a href="#" class="btn btn-outline-light bg-primary px-4 py-2 text-white rounded">Babies</a>
                                </div>

                                <div class="carousel-item rounded">
                                    <img src="assets/images/Kurta set.jpg" class="img-fluid w-100 h-100 rounded" alt="Second slide">
                                    <a href="#" class="btn btn-outline-light bg-primary px-4 py-2 text-white rounded">Women</a>
                                </div>

                                <div class="carousel-item rounded">
                                    <img src="assets/images/unisex hiphop tshirt.jpg" class="img-fluid w-100 h-100 rounded" alt="Second slide">
                                    <a href="#" class="btn btn-outline-light bg-primary px-4 py-2 text-white rounded">Free Sized</a>
                                </div>

                            </div>

                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero End -->



        <!-- Features Section Start -->
        <style>
            /* Container background */
            .featurs {
                background-color: #f8f9fa;
            }

            /* Feature item base styles */
            .featurs-item {
                background-color: #f8f9fa;
                background-clip: padding-box;
                border-radius: 0.5rem;
                padding: 1.5rem;
                text-align: center;
                background-color: #f8f9fa;
                cursor: pointer;

                /* Transition for smooth hover */
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            /* Hover effect with green shadow and slight scale */
            .featurs-item:hover {
                transform: scale(1.05);
                box-shadow: 0 8px 16px rgba(0, 128, 0, 0.4); /* green shadow */
                z-index: 2;
            }

            /* Icon container common styles */
            .featurs-icon {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                margin: 0 auto 1rem auto;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Gradient backgrounds for icons */
            .bg-gradient-blue {
                background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            }

            .bg-gradient-orange {
                background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
            }

            .bg-gradient-green {
                background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
            }

            .bg-gradient-red {
                background: linear-gradient(135deg, #ff5f6d 0%, #ffc371 100%);
            }

            /* Text styling */
            .featurs-content h5 {
                margin-bottom: 0.5rem;
            }

            .text-muted {
                color: #6c757d;
            }
        </style>

        <div class="container-fluid featurs py-5" style="background-color: #f8f9fa;">
            <div class="container py-5">
                <div class="row g-4">

                    <!-- Feature Item 1 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="featurs-item text-center rounded bg-light p-4">
                            <div class="featurs-icon btn-square rounded-circle mb-4 mx-auto d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); width: 80px; height: 80px;">
                                <i class="fas fa-shipping-fast fa-3x text-white"></i>
                            </div>
                            <div class="featurs-content">
                                <h5 class="mb-2">Free Shipping</h5>
                                <p class="text-muted">On orders above Rs 50000, fast &amp; secure delivery with no delivery charges.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature Item 2 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="featurs-item text-center rounded bg-light p-4">
                            <div class="featurs-icon btn-square rounded-circle mb-4 mx-auto d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); width: 80px; height: 80px;">
                                <i class="fas fa-lock fa-3x text-white"></i>
                            </div>
                            <div class="featurs-content">
                                <h5 class="mb-2">Secure Payment</h5>
                                <p class="text-muted">100% safe transactions with modern encryption.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature Item 3 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="featurs-item text-center rounded bg-light p-4">
                            <div class="featurs-icon btn-square rounded-circle mb-4 mx-auto d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%); width: 80px; height: 80px;">
                                <i class="fas fa-credit-card fa-3x text-white"></i>
                            </div>
                            <div class="featurs-content">
                                <h5 class="mb-2">Buy Now Pay Later</h5>
                                <p class="text-muted">Exclusive credit available for our loyal customers with repeat purchases.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature Item 4 -->
                    <div class="col-md-6 col-lg-3">
                        <div class="featurs-item text-center rounded bg-light p-4">
                            <div class="featurs-icon btn-square rounded-circle mb-4 mx-auto d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #ff5f6d 0%, #ffc371 100%); width: 80px; height: 80px;">
                                <i class="fas fa-headset fa-3x text-white"></i>
                            </div>
                            <div class="featurs-content">
                                <h5 class="mb-2">24/7 Support</h5>
                                <p class="text-muted">We are here anytime you need help.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- Features Section End -->