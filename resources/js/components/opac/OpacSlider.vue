<template>
  <div class="slider-container mt-1">
    <div class="container-xl">
      <div id="mainCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner">
          <div v-for="(image, index) in sliderImages" :key="`slide-${index}`" 
               :class="['carousel-item', index === 0 ? 'active' : '']">
            <div class="carousel-image-container">
              <img :src="getSliderImageUrl(image)" class="d-block" :alt="`Slide ${index+1}`">
            </div>
            <div class="carousel-overlay"></div>
            <div class="carousel-caption">
              <h2>Thư viện trường Đại học Sư phạm Kỹ thuật Vĩnh Long</h2>
              <p>Nguồn tri thức vô tận cho sinh viên và giảng viên</p>
            </div>
          </div>
        </div>
        
        <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
        
        <!-- Điều hướng dưới slider -->
        <div class="slider-navigation">
          <div class="slider-dots">
            <span v-for="(image, index) in sliderImages" :key="`dot-${index}`" 
                  :class="['dot', index === activeDotIndex ? 'active' : '']"
                  @click="goToSlide(index)"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "OpacSlider",
  data() {
    return {
      sliderImages: [
        'slider1.png',
        'slider2.jpg',
        'slider3.jpg',
        'slider4.jpg',
        'slider5.jpg'
      ],
      activeDotIndex: 0,
      carouselInstance: null
    }
  },
  mounted() {
    // Khởi tạo carousel khi component được mount
    this.initCarousel();
    
    // Đảm bảo carousel được khởi tạo lại nếu cần
    window.addEventListener('resize', this.handleResize);
    
    // Preload các hình ảnh để tránh trễ khi chuyển slide
    this.preloadImages();
  },
  beforeUnmount() {
    // Dọn dẹp
    window.removeEventListener('resize', this.handleResize);
  },
  methods: {
    getSliderImageUrl(image) {
      return `/images/image_slider/${image}`;
    },
    goToSlide(index) {
      if (this.carouselInstance) {
        this.carouselInstance.to(index);
        this.activeDotIndex = index;
      }
    },
    initCarousel() {
      // Theo dõi các sự kiện của Bootstrap carousel
      const carouselElement = document.getElementById('mainCarousel');
      if (carouselElement) {
        // Lưu instance để có thể điều khiển carousel
        this.carouselInstance = new bootstrap.Carousel(carouselElement, {
          interval: 5000,
          keyboard: true,
          pause: 'hover',
          wrap: true,
          touch: true
        });
        
        carouselElement.addEventListener('slide.bs.carousel', (event) => {
          this.activeDotIndex = event.to;
        });
      }
    },
    handleResize() {
      // Khởi tạo lại carousel nếu cần
      if (!this.carouselInstance) {
        this.initCarousel();
      }
    },
    preloadImages() {
      // Preload tất cả các hình ảnh để tránh hiện tượng trắng khi chuyển slide
      this.sliderImages.forEach(image => {
        const img = new Image();
        img.src = this.getSliderImageUrl(image);
      });
    }
  }
}
</script>

<style scoped>
.slider-container {
  position: relative;
  margin-bottom: 2rem;
}

.carousel {
  overflow: hidden;
  border-radius: 8px;
}

.carousel-fade .carousel-item {
  opacity: 0;
  transition: opacity 0.6s ease-in-out;
}

.carousel-fade .carousel-item.active {
  opacity: 1;
}

.carousel-item {
  position: relative;
  height: 400px; /* Giảm chiều cao xuống để tỷ lệ hợp lý hơn */
}

.carousel-image-container {
  width: 100%;
  height: 100%;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
}

.carousel-image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover; /* Đảm bảo ảnh không bị méo */
  object-position: center;
  transition: transform 0.6s ease;
}

.carousel-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.3); /* Lớp overlay tối để text dễ đọc */
  border-radius: 8px;
}

.carousel-caption {
  position: absolute;
  bottom: 50%;
  left: 50%;
  transform: translate(-50%, 50%);
  text-align: center;
  width: 80%;
  max-width: 900px;
  padding: 1.5rem;
  background-color: rgba(0, 0, 0, 0.5);
  border-radius: 10px;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
}

.carousel-caption h2 {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: #fff;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.carousel-caption p {
  font-size: 1.1rem;
  color: rgba(255, 255, 255, 0.9);
  max-width: 600px;
  margin: 0 auto;
}

.carousel-control-prev,
.carousel-control-next {
  opacity: 0.7;
  width: 5%;
  z-index: 10;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
  width: 2.5rem;
  height: 2.5rem;
}

/* Điều hướng dots */
.slider-navigation {
  background: rgba(0, 0, 0, 0.6);
  padding: 12px 0;
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  z-index: 10;
  border-radius: 0 0 8px 8px;
  width: 100%;
}

.slider-dots {
  text-align: center;
}

.dot {
  display: inline-block;
  width: 12px;
  height: 12px;
  margin: 0 6px;
  background-color: rgba(255, 255, 255, 0.5);
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.3s ease;
}

.dot.active {
  background-color: white;
  transform: scale(1.2);
}

.dot:hover {
  background-color: rgba(255, 255, 255, 0.8);
}

@media (max-width: 992px) {
  .carousel-item {
    height: 350px;
  }
  
  .carousel-caption h2 {
    font-size: 1.75rem;
  }
  
  .carousel-caption p {
    font-size: 1rem;
  }
}

@media (max-width: 768px) {
  .carousel-item {
    height: 300px;
  }
  
  .carousel-caption {
    padding: 1rem;
  }
  
  .carousel-caption h2 {
    font-size: 1.5rem;
  }
}

@media (max-width: 576px) {
  .carousel-item {
    height: 250px;
  }
  
  .carousel-caption {
    padding: 0.75rem;
    width: 90%;
  }
  
  .carousel-caption h2 {
    font-size: 1.25rem;
    margin-bottom: 0.25rem;
  }
  
  .carousel-caption p {
    font-size: 0.875rem;
  }
}
</style>