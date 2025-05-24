<template>
  <div class="slider-container mt-1">
    <div class="container-xl">
      <div id="mainCarousel" class="carousel slide carousel-fade" data-ride="carousel">
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
        
        <a class="carousel-control-prev" href="#mainCarousel" role="button" data-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#mainCarousel" role="button" data-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
        </a>
        
        <!-- Điều hướng dưới slider -->
        <ol class="carousel-indicators">
          <li v-for="(image, index) in sliderImages" :key="`indicator-${index}`"
              :data-target="'#mainCarousel'" :data-slide-to="index"
              :class="[index === activeDotIndex ? 'active' : '']"></li>
        </ol>
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
      carouselInterval: null
    }
  },
  mounted() {
    // Tự động chuyển slide
    this.startCarouselInterval();
    
    // Lắng nghe sự kiện carousel từ jQuery
    if (window.jQuery) {
      $('#mainCarousel').on('slide.bs.carousel', (event) => {
        this.activeDotIndex = $(event.relatedTarget).index();
      });
    }
  },
  beforeUnmount() {
    // Dừng interval khi component bị hủy
    this.clearCarouselInterval();
  },
  methods: {
    getSliderImageUrl(image) {
      return `/images/image_slider/${image}`;
    },
    startCarouselInterval() {
      // Tự động chuyển slide mỗi 5 giây nếu AdminLTE không tự xử lý
      this.carouselInterval = setInterval(() => {
        if (window.jQuery) {
          $('#mainCarousel').carousel('next');
        } else {
          this.activeDotIndex = (this.activeDotIndex + 1) % this.sliderImages.length;
          // Cần gọi carousel thủ công nếu không có jQuery
        }
      }, 5000);
    },
    clearCarouselInterval() {
      if (this.carouselInterval) {
        clearInterval(this.carouselInterval);
      }
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
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.carousel-item {
  position: relative;
  height: 400px;
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
  object-fit: cover;
  object-position: center;
}

.carousel-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.3);
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

.carousel-indicators {
  bottom: 10px;
}

.carousel-indicators li {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  margin: 0 5px;
  background-color: rgba(255, 255, 255, 0.5);
  cursor: pointer;
  transition: all 0.3s ease;
}

.carousel-indicators li.active {
  background-color: white;
  transform: scale(1.2);
}

@media (max-width: 992px) {
  .carousel-item {
    height: 350px;
  }
  
  .carousel-caption h2 {
    font-size: 1.5rem;
  }
  
  .carousel-caption p {
    font-size: 0.9rem;
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
    font-size: 1.2rem;
  }
}

@media (max-width: 576px) {
  .carousel-item {
    height: 250px;
  }
  
  .carousel-caption h2 {
    font-size: 1rem;
  }
  
  .carousel-caption p {
    font-size: 0.8rem;
  }
}
</style>