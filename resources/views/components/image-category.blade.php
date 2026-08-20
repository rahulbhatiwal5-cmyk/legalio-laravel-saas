<div class="in_box_cate">
    <a href="{{ url('/'.$category->category->slug) }}">
        <div class="in_img_cate">
            <img src="{{ asset('storage/'.$path ?? '' ) }}" alt="">
        </div>
    </a>
    <a href="{{ url('/'.$category->category->slug) }}">
        <div class="in_cate_content">
            <h3>{{ $category->heading ?? '' }}</h3>
            <p class="in_cate_para">
                {{ $category->category_description ?? '' }}
            </p>
        </div>
    </a>
    <div class="cata_btn">
        <a href="{{ url('/'.$category->category->slug) }}" class="cta_org">{{ $category->btn_text ?? '' }}  <i class="fa-solid fa-arrow-right-long"></i></a>
    </div>
</div>