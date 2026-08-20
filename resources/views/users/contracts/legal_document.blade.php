@extends('users_layout.master')
 @php $hideHeader = true; @endphp
<!-- @extends('users_layout.master') -->
@section('title',$legal->meta_title)
@section('content')
@include('users_layout.categories_header')

<section class="tab_sec_ot privacy-sec">
    {{-- <div class="cate_hed"> --}}
          <div class="container">
               <div class="row">
                    <div class="heading_sec_tabs">
                         <h2 class="doc_h">{{ $legal->main_heading ?? 'Elige el Documento Legal que necesitas' }}</h2>
                         <p class="size18 light">{{ $legal->main_sub_heading ?? 'Crea documentos legales personalizados que se adaptan a tus necesidades' }}</p>
                    </div>
               </div>
          </div>
     {{-- </div> --}}

     <div class="container">
          <div class="wrapper">
               <div class="tab categories-tab">
                    <!-- <div class="btn " onclick="showTab('todos')">
                         Todos
                    </div> -->
                    @if(isset($document_category) && $document_category != null)
                    @foreach($document_category as $index => $doc_catg)
                         <!-- <div class="btn {{ $index == 0 ? 'active' : '' }}" onclick="showTab('tab{{ $index + 1 }}')">
                         {{ $doc_catg->name ?? '' }}
                         </div> -->
                         <a href="{{ url('/'.$doc_catg->slug ?? '' ) }}" class="btn">
                              {{ $doc_catg->name ?? '' }}
                         </a>
                    @endforeach
                    @endif
               </div>

               <div class="tabContentWrap">
                    <!-- Todos Tab Content -->
                    <div class="tab_box_sec tabContent show" id="todos">
                         <div class="container">
                              <div class="row categry-rw">
                              @if(isset($alldocuments) && $alldocuments != null)
                                   @foreach($alldocuments as $document)
                                   <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="inside_box_b">
                                             <div class="inside_box_tab">
                                                  <a href="{{ url('document/'.$document->slug) }}" class="contract_link">
                                                       <div class="img_tab_sec">
                                                            <?php
                                                                 $image_path = $document->document_image;
                                                            ?>
                                                            <img src="{{ $image_path ?? '' }}" alt="">
                                                       </div>
                                                  </a>
                                                  <div class="cont_tab_ot">
                                                       <a href="{{ url('document/'.$document->slug) }}" class="contract_link">
                                                            <div class="tab_text">
                                                                 <h5 class="size20">
                                                                      {{ $document->title ?? '' }}
                                                                 </h5>
                                                                 @php
													     $avgRating = $document->getavgRating();
                                                                      $allDocumentRating = \App\Models\Document::getAllDocumentAvgRating();
                                                                 @endphp

                                                                 @if($avgRating !== false)
                                                                      <x-rating-component :rating="$avgRating" />
                                                                 @else
                                                                      <x-rating-component :rating="$allDocumentRating" ratingClass="cont_li" ratingText="Legalio Average " :showDescription="true"  />
                                                                 @endif 


                                                                 {{-- <div class="tab_ul">
                                                                      <div class="tab_star_li">
                                                                           <span class="rating-on rate-1" data-rating="1"></span>
                                                                           <span class="rating-on rate-2" data-rating="2"></span>
                                                                           <span class="rating-on rate-3" data-rating="3"></span>
                                                                           <span class="rating-on rate-4" data-rating="4"></span>
                                                                           <span class="rating-on rate-5" data-rating="5"></span>
                                                                      </div>
                                                                      <div>4.6</div>
                                                                 </div> --}}
                                                                 
                                                            </div>
                                                            <!-- <div class="tab_2text light">
                                                                 @php $short = Str::limit($document->short_description, 70, '...');
                                                                      print_r($short);
                                                                 @endphp
                                                            </div> -->
                                                       </a>
                                                  </div>
                                                  <div class="tab_btn">
                                                       <a href="{{ url('document/'.$document->slug) }}" class="cta_org">Create</a>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   @endforeach
                              @endif
                              </div>
                         </div>
                    </div>

                    <!-- Category Tabs Content -->
                    <!-- @if(isset($document_category) && $document_category != null)
                         @foreach($document_category as $index => $catg)
                              <div class="tab_box_sec tabContent {{ $index == 0 ? 'show' : '' }}" id="tab{{ $index + 1 }}">
                              <div class="container">
                                   <div class="row">
                                        @foreach($alldocuments as $catg_document)
                                             @if($catg_document->categories->contains('id', $catg->id))
                                             <div class="col-lg-3 col-md-6 col-sm-6">
                                                  <div class="inside_box_b">
                                                       <div class="inside_box_tab">
                                                            <a href="{{ url('document/'.$catg_document->slug) }}" class="contract_link">
                                                                 <div class="img_tab_sec">
                                                                      <?php
                                                                      $document_img = getStorageFilepath($catg_document->document_file_path);
                                                                      ?>
                                                                      <img src="{{ asset('storage/'.$document_img) }}" alt="">
                                                                 </div>
                                                            </a>
                                                            <div class="cont_tab_ot">
                                                                 <a href="{{ url('document/'.$catg_document->slug) }}" class="contract_link">
                                                                      <div class="tab_text">
                                                                           <h5 class="size20">{{ $catg_document->title ?? '' }}</h5>
                                                                           <div class="tab_ul">
                                                                                <div class="tab_star_li">
                                                                                     <span class="rating-on rate-1" data-rating="1"></span>
                                                                                     <span class="rating-on rate-2" data-rating="2"></span>
                                                                                     <span class="rating-on rate-3" data-rating="3"></span>
                                                                                     <span class="rating-on rate-4" data-rating="4"></span>
                                                                                     <span class="rating-on rate-5" data-rating="5"></span>
                                                                                </div>
                                                                                <div>4.6</div>
                                                                           </div>
                                                                      </div>
                                                                      <div class="tab_2text light">
                                                                           <?php $short = Str::limit($catg_document->short_description, 70, '...');
                                                                           print_r($short); ?>
                                                                      </div>
                                                                 </a>
                                                            </div>
                                                            <div class="tab_btn">
                                                                 <a href="{{ url('document/'.$catg_document->slug) }}" class="cta_org">Crear ahora</a>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                             @endif
                                        @endforeach
                                   </div>
                              </div>
                              </div>
                         @endforeach
                    @endif -->
                    <!-- Pagination-->
                    <nav class="pagination_nav" aria-label="Page navigation example">
                         <div class="container">
                         <ul class="pagination justify-content-center">
                              @if($alldocuments->onFirstPage())
                                   <li class="page-item disabled">
                                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                                        <i class="fa-solid fa-arrow-left"></i>
                                        </a>
                                   </li>
                              @else
                                   <li class="page-item active">
                                        <a class="page-link" href="/legal-documents/{{ $alldocuments->currentPage() - 1 }}">
                                        <i class="fa-solid fa-arrow-left"></i>
                                        </a>
                                   </li>
                              @endif

                              @for($i = 1; $i <= $alldocuments->lastPage(); $i++)
                                   <li class="page-item {{ $i == $alldocuments->currentPage() ? 'active' : '' }}">
                                        <a class="page-link" href="/legal-documents/{{ $i }}">{{ $i }}</a>
                                   </li>
                              @endfor
                              @if($alldocuments->hasMorePages())
                                   <li class="page-item active">
                                        <a class="page-link" href="/legal-documents/{{ $alldocuments->currentPage() + 1 }}">
                                        <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                   </li>
                              @else
                                   <li class="page-item disabled">
                                        <a class="page-link" href="#">
                                        <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                   </li>
                              @endif
                         </ul>
                    </div>
                    </nav>
               </div>
          </div>
     </div>
</section>


<script>
     function showTab(tabId) {
          document.querySelectorAll('.tabContent').forEach(tab => tab.classList.remove('show'));
          document.querySelectorAll('.btn').forEach(btn => btn.classList.remove('active'));
          document.getElementById(tabId).classList.add('show');
          document.querySelector(`[onclick="showTab('${tabId}')"]`).classList.add('active');
     }

     document.addEventListener('DOMContentLoaded', function() {
          showTab('todos');
     });
</script>



@endsection
