<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AiPromptController;
use App\Http\Controllers\Admin\AllPagesController;
use App\Http\Controllers\Admin\DocumentBetaController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\DocumentGeneratingPromptController;
use App\Http\Controllers\Admin\DocumentRightController;
use App\Http\Controllers\Admin\GlobalController;
use App\Http\Controllers\Admin\ImageTextController;
use App\Http\Controllers\Admin\KnowledgeBaseController;
use App\Http\Controllers\Admin\MetaDataController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PartiesSectionTemplateController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewerController;
use App\Http\Controllers\Admin\SiteMetaController;
use App\Http\Controllers\Admin\StateSpecificClauseController;
use App\Http\Controllers\Admin\SupportAssistantController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/admin-logout', [AuthController::class, 'adminLogout']);

Route::group(['middleware' => ['admin']], function () {
    Route::get('/admin-dashboard', [AdminController::class, 'index'])->name('admin.dashboard.index');
    Route::get('/admin-dashboard/filter', [AdminController::class, 'filter'])->name('admin.dashboard.filter');
    Route::get('/admin-dashboard/country', [AdminController::class, 'country'])->name('admin.dashboard.country');

    Route::get('/admin-dashboard/documents/add', [DocumentController::class, 'documents'])->name('admin.dashboard.addDocuments');
    Route::post('/admin-dashboard/add-documents', [DocumentController::class, 'addDocuments'])->name('admin.dashboard.add_documents');
    Route::get('/admin-dashboard/documents', [DocumentController::class, 'allDocuments'])->name('admin.dashboard.documents');
    Route::get('/admin-dashboard/documents/edit/{slug}', [DocumentController::class, 'editDocument'])->name('admin.dashboard.edit_documents');
    Route::post('/admin-dashboard/update-document', [DocumentController::class, 'updateDocument'])->name('admin.dashboard.update_documents');
    Route::get('/admin-dashboard/delete-document/{id}', [DocumentController::class, 'deleteDocument'])->name('delete.document');
    Route::post('/admin-dashboard/update-document-slug', [DocumentController::class, 'updateDocumentSlug'])->name('update.document.slug');
    Route::post('/admin-dashboard/update-document-image', [DocumentController::class, 'updateDocumentImage'])->name('update.document.image');
    Route::post('/admin-dashboard/update-documentField-image', [DocumentController::class, 'saveGeneratedAiImage'])->name('admin.dashboard.update_documentField_image');

    Route::post('/ai/autofill/generate-keywords', [DocumentController::class, 'generateKeywords'])->name('ai.autofill.generateKeywords');
    Route::get('/admin-dashboard/get-document/{id}', [DocumentController::class, 'getDocument'])->name('get.document.admin');
    Route::post('/admin-dashboard/api/update-document', [DocumentController::class, 'updateDocumentApi'])->name('update.document.api');

    Route::controller(DocumentBetaController::class)->group(function () {
        Route::get('/admin-dashboard/documents/beta', 'allBetaDocuments')->name('admin.dashboard.documents.beta');
        Route::get('/admin-dashboard/document-generator/beta', 'betadocumentGenerator')->name('admin.generate.document.beta');
        Route::post('/admin-dashboard/add-document-generator/beta', 'betadocumentGenerateProcess')->name('admin.document.generateProcc.beta');
        Route::post('/admin-dashboard/document-generator/questionnaire-step', 'questionnaireStep')->name('admin.document.questionnaireStep.beta');
        Route::post('/admin-dashboard/document-generator/contract-step', 'contractStep')->name('admin.document.contractStep.beta');
        Route::post('/admin-dashboard/documents/update', 'updateDocument')->name('admin.dashboard.update_documents_beta');
        Route::get('/admin-dashboard/document-generator/state-clauses', 'getStateClauses')->name('admin.state-clauses.get');
    });

    Route::get('/admin-dashboard/notifications/add', [NotificationController::class, 'notifications'])->name('admin.dashboard.Notifications');
    Route::get('/admin-dashboard/notifications', [NotificationController::class, 'index'])->name('admin.dashboard.notifications');
    Route::post('/admin-dashboard/add-notifications', [NotificationController::class, 'addNotifications'])->name('admin.dashboard.add_notifications');
    Route::get('/admin-dashboard/notifications/{id}/edit', [NotificationController::class, 'editNotification'])->name('admin.notifications.edit');
    Route::put('/admin-dashboard/notifications/{id}', [NotificationController::class, 'updateNotification'])->name('admin.notifications.update');
    Route::delete('/admin-dashboard/notifications/{id}', [NotificationController::class, 'destroy'])->name('admin.notifications.destroy');

    Route::post('/ai/autofill/save-document', [DocumentController::class, 'saveDocument'])->name('ai.autofill.saveDocument');
    Route::post('/update/documentField/image', [DocumentController::class, 'updateFieldImage'])->name('admin.dashboard.update_image');
    Route::get('/admin-dashboard/general-section', [DocumentController::class, 'generalSection'])->name('admin.dashboard.general_section');
    Route::post('/admin-dashboard/add/general-section', [DocumentController::class, 'addGeneralSection'])->name('admin.dashboard.add_general_section');
    Route::post('/update/agreement/image', [DocumentController::class, 'addNewImage'])->name('admin.dashboard.update_agreement_image');
    Route::get('/admin-dashboard/article-section', [DocumentController::class, 'articleSection'])->name('admin.dashboard.article_section');
    Route::post('/admin-dashboard/add/article-section', [DocumentController::class, 'addArticleSection'])->name('admin.dashboard.add_article_section');

    Route::get('/admin-dashboard/all-document-questions', [DocumentController::class, 'allQuestion'])->name('admin.dashboard.all_document_questions');
    Route::get('/admin-dashboard/document-questions', [DocumentController::class, 'documentQuestion'])->name('admin.dashboard.document_questions');
    Route::post('/admin-dashboard/add/document-questions', [DocumentController::class, 'addDocumentQuestion'])->name('admin.dashboard.add_document_questions');
    Route::get('/admin-dashboard/all-question-type', [DocumentController::class, 'allquestionType'])->name('admin.dashboard.all_question_type');
    Route::get('/admin-dashboard/question-type', [DocumentController::class, 'questionType'])->name('admin.dashboard.question_type');
    Route::post('/admin-dashboard/add-question-type', [DocumentController::class, 'addTypes'])->name('admin.dashboard.add_question_type');
    Route::get('/admin-dashboard/edit-question-type/{slug}', [DocumentController::class, 'editQuestionType'])->name('admin.dashboard.edit_question_type');

    Route::get('/admin-dashboard/all-document-right-content', [DocumentRightController::class, 'allRightContent'])->name('admin.dashboard.all_document_right_content');
    Route::get('/admin-dashboard/document-right-content', [DocumentRightController::class, 'documentRightContent'])->name('admin.dashboard.document_right_content');
    Route::post('/admin-dashboard/add-document-right-content', [DocumentRightController::class, 'addDocumentRightContent'])->name('admin.dashboard.add_document_right_content');
    Route::get('/admin-dashboard/edit-document-right-content/{id}', [DocumentRightController::class, 'editRightContent'])->name('admin.dashboard.edit_document_right_content');
    Route::post('/admin-dashboard/update-document-right-content', [DocumentRightController::class, 'updateRightContent'])->name('admin.dashboard.update_document_right_content');

    Route::get('/admin-dashboard/document-generator', [DocumentController::class, 'documentGenerator'])->name('admin.generate.document');
    Route::post('/admin-dashboard/add-document-generator', [DocumentController::class, 'documentGenerateProcess'])->name('admin.document.generateProcc');
    Route::get('/admin-dashboard/document/graphical/interface', [DocumentController::class, 'graphicalInterface'])->name('admin.document.graphical_interface');
    Route::post('/admin-dashbord/document/feedback', [DocumentController::class, 'sendFeedbackToAi'])->name('admin.document.feedback');
    Route::post('/admin-dashboard/save-recommended-section', [DocumentController::class, 'saveRecommendedSection'])->name('admin.save.recommended.section');
    Route::post('/admin-dashboard/update-recommended-section', [DocumentController::class, 'updateRecommendedSection'])->name('admin.update.recommended.section');
    Route::post('/admin-dashboard/delete-document-questions', [DocumentController::class, 'deleteDocumentQuestions'])->name('admin.delete.document_questions');
    Route::post('/admin-dashboard/generate-section-ids', [DocumentController::class, 'documentSectionProcess'])->name('admin.generate.section');
    Route::post('/admin-dashboard/edit-questions', [DocumentController::class, 'editGeneratedQuestions'])->name('admin.edit.questions');
    Route::post('/admin-dashboard/check-document-title', [DocumentController::class, 'checkTitle'])->name('admin.check.document.title');
    Route::post('/admin-dashboard/save-generator-step', [DocumentController::class, 'saveGeneratorStep'])->name('admin.generator.save_final_step');
    Route::get('/admin-dashboard/ai-response/{id}', [DocumentController::class, 'aiResponse'])->name('admin.ai.response');
    Route::post('/admin-dashboard/save-json-response', [DocumentController::class, 'saveDocumentGeneratorData'])->name('admin.save.document.json');
    Route::post('/admin-dashboard/check-status', [DocumentController::class, 'checkStatus'])->name('admin.check.document.status');

    Route::get('/admin-dashboard/standard/section', [GlobalController::class, 'standardSection'])->name('admin.document.standard_section');
    Route::get('/admin-dashboard/standard/section/document', [GlobalController::class, 'standardDocument'])->name('admin.document.standard_document');
    Route::post('/admin-dashboard/standard/section/document/add', [GlobalController::class, 'addStandardDocument'])->name('admin.document.add_standard_document');
    Route::get('/admin-dashboard/standard/section/document/edit/{slug}', [GlobalController::class, 'editStandardDocument'])->name('admin.document.edit_standard_document');
    Route::get('/admin-dashboard/standard/section/questions/{id}', [GlobalController::class, 'contractQuestion'])->name('admin.global.question');
    Route::post('/admin-dashboard/standard/section/questions/add', [GlobalController::class, 'addContractQuestions'])->name('admin.global.questions.add');
    Route::get('/admin-dashboard/standard/section/text/{id}', [GlobalController::class, 'contractText'])->name('admin.global.text');
    Route::post('/admin-dashboard/standard/section/text/add', [GlobalController::class, 'addContractText'])->name('admin.global.text.add');
    Route::get('/admin-dashboard/standard/section/delete/{slug}', [GlobalController::class, 'deleteStandardDocument'])->name('admin.document.delete_standard_document');
    Route::post('/admin-dashboard/standard/section/document/add-state-version', [GlobalController::class, 'addStateVersion'])->name('admin.document.add_state_version');
    Route::delete('/admin-dashboard/standard/section/document/delete-state-version/{id}', [GlobalController::class, 'deleteStateVersion'])->name('admin.document.delete_state_version');
    Route::patch('/admin-dashboard/standard/section/document/update-state-version/{id}', [GlobalController::class, 'updateStateVersion'])->name('admin.document.update_state_version');
    Route::get('/admin-dashboard/standard/document-contract-edit', [GlobalController::class, 'standardContractEdit'])->name('standard.contract.edit');
    Route::get('/document/toggle-status/{slug}', [GlobalController::class, 'toggleStandardDocumentStatus'])->name('admin.document.toggle_standard_document_status');
    Route::get('/admin-dashboard/standard/section/documents/api', [GlobalController::class, 'getStandardDocumentsForContract'])->name('admin.document.standard_documents_api');

    Route::get('/admin-dashboard/configuration', [SiteMetaController::class, 'configuration'])->name('admin.config.reviews');
    Route::post('/admin-dashboard/configuration/update', [SiteMetaController::class, 'updateConfiguration'])->name('admin.config.update');
    Route::get('/admin-dashboard/reviews', [DocumentController::class, 'reviews'])->name('admin.reviews');
    Route::post('/admin-dashboard/add-review', [DocumentController::class, 'addReview'])->name('admin.add_reviews');
    Route::get('/admin-dashboard/published-reviews', [DocumentController::class, 'publishedReview'])->name('admin.dashboard.published_reviews');
    Route::get('/admin-dashboard/edit-review/{id}', [DocumentController::class, 'editReview'])->name('admin.dashboard.edit_review');
    Route::post('/admin-dashboard/delete-review', [DocumentController::class, 'deleteReview'])->name('admin.dashboard.delete_review');
    Route::post('/admin-dashboard/publish-review', [DocumentController::class, 'reviewStatus'])->name('admin.dashboard.publish_review');
    Route::get('/admin-dashboard/pending-reviews', [DocumentController::class, 'pendingReviews'])->name('admin.dashboard.pending_reviews');
    Route::post('/admin-dashboard/reject-reviews', [DocumentController::class, 'rejectReviews'])->name('admin.dashboard.reject_reviews');

    Route::get('/admin-dashboard/add-document-category', [DocumentController::class, 'addDocumentCategory'])->name('add.category');
    Route::post('/admin-dashboard/category-process', [DocumentController::class, 'CategoryProcess'])->name('category.process');
    Route::get('/admin-dashboard/document/categories', [DocumentController::class, 'allCategories'])->name('document.categories');
    Route::get('/admin-dashboard/edit-category/{slug}', [DocumentController::class, 'editCategory'])->name('edit.category');
    Route::post('/admin-dashboard/delete-category/{id}', [DocumentController::class, 'deleteCategory'])->name('delete.category');
    Route::get('/admin-dashboard/api/categories', [DocumentController::class, 'categoriesApi'])->name('api.categories');

    Route::get('/admin-dashboard/how-it-works', [SiteMetaController::class, 'howItWorks'])->name('admin.dashboard.how_it_works');
    Route::post('/admin-dashboard/add-how-it-works', [SiteMetaController::class, 'addHowItWorks'])->name('admin.dashboard.add_how-it_works');
    Route::post('/update/work/image', [SiteMetaController::class, 'updateWorkImage'])->name('admin.dashboard.update_work_image');
    Route::post('/admin-dashboard/deleteworkSec', [SiteMetaController::class, 'deleteWorks'])->name('admin.dashboard.deleteworkSec');

    Route::get('/admin-dashboard/terms-and-conditions', [SiteMetaController::class, 'termsConditions'])->name('admin.dashboard.terms_and_conditions');
    Route::post('/admin-dashboard/add-terms-process', [SiteMetaController::class, 'addTermsAndCondition'])->name('admin.dashboard.add_terms_process');
    Route::get('/admin-dashboard/help-center', [SiteMetaController::class, 'helpCenter'])->name('admin.dashboard.help_center');
    Route::post('/admin-dashboard/help-center-proccess', [SiteMetaController::class, 'helpProcc'])->name('admin.dashboard.help_center_proccess');
    Route::post('/update/help/image', [SiteMetaController::class, 'updateHelpImage'])->name('admin.dashboard.update_help_image');

    Route::get('/admin-dashboard/who-we-are', [AllPagesController::class, 'aboutUs'])->name('admin.dashboard.who_we_are');
    Route::post('/admin-dashboard/add/who-we-are', [AllPagesController::class, 'whoWeAre'])->name('admin.dashboard.add_who_we_are');
    Route::post('/update/vision/image', [AllPagesController::class, 'updateVisionImage'])->name('admin.dashboard.update_vision_image');

    Route::get('/admin-dashboard/login', [SiteMetaController::class, 'login'])->name('admin.dashboard.login');
    Route::post('/admin-dashboard/add-login', [SiteMetaController::class, 'addLogin'])->name('admin.dashboard.add_login');
    Route::get('/admin-dashboard/register', [SiteMetaController::class, 'register'])->name('admin.dashboard.register');
    Route::post('/admin-dashboard/add-register', [SiteMetaController::class, 'addRegister'])->name('admin.dashboard.add_register');
    Route::get('/admin-dashboard/prices', [SiteMetaController::class, 'prices'])->name('admin.dashboard.prices');
    Route::post('/admin-dashboard/add-price', [SiteMetaController::class, 'addPriceContent'])->name('admin.dashboard.add_price');
    Route::get('/admin-dashboard/faq', [AllPagesController::class, 'faq'])->name('admin.dashboard.faq');
    Route::post('/admin-dashboard/faq-process', [AllPagesController::class, 'faqAdd'])->name('admin.dashboard.faq_process');
    Route::get('/admin-dashboard/faq-category', [AllPagesController::class, 'allFaqCategory'])->name('admin.dashboard.faq_category');
    Route::get('/admin-dashboard/add/faq-category', [AllPagesController::class, 'faqCategory'])->name('admin.dashboard.add_faq_category');
    Route::post('/admin-dashboard/add/procc', [AllPagesController::class, 'addCategory'])->name('admin.dashboard.add_procc');
    Route::get('/admin-dashboard/edit/faq-category/{slug}', [AllPagesController::class, 'editFaqCategory'])->name('admin.dashboard.edit_faq_category');
    Route::post('classicEditor/upload-image', [AllPagesController::class, 'uploadEditorImage']);
    Route::get('/admin-dashboard/delete/faq-category/{slug}', [AllPagesController::class, 'deleteFaqCategory'])->name('admin.dashboard.delete_faq_category');
    Route::get('/admin-dashboard/privacy-policy', [AllPagesController::class, 'privecyPolicy'])->name('admin.dashboard.privacy_policy');
    Route::post('/admin-dashboard/privacy-policy-process', [AllPagesController::class, 'addPrivacyPolicy'])->name('admin.dashboard.add_privacy_policy_process');
    Route::post('/admin-dashboard/privacy-policy-remove', [AllPagesController::class, 'removePolicy'])->name('admin.dashboard.privacy_policy_remove');
    Route::get('/admin-dashboard/legal-notice', [AllPagesController::class, 'legalNotice'])->name('admin.dashboard.legal_notice');
    Route::post('/admin-dashboard/legal-notice-process', [AllPagesController::class, 'addLegalNotice'])->name('admin.dashboard.add_legal_notice_process');
    Route::get('/admin-dashboard/contact-us', [SiteMetaController::class, 'contactUs'])->name('admin.dashboard.contact_us');
    Route::post('/admin-dashboard/contact-us-procc', [SiteMetaController::class, 'addContactProcc'])->name('admin.dashboard.contact_us_procc');
    Route::get('/admin-dashboard/prepare-contract', [SiteMetaController::class, 'prepareContract'])->name('admin.dashboard.prepare_contract');
    Route::post('/admin-dashboard/prepare-contract-procc', [SiteMetaController::class, 'prepareContractprocc'])->name('admin.dashboard.prepare_contract_procc');
    Route::get('/admin-dashboard/home-content', [SiteMetaController::class, 'homepage'])->name('admin.dashboard.home_content');
    Route::post('/admin-dashboard/add/home-content', [SiteMetaController::class, 'addHomeContent'])->name('admin.dashboard.add_home_content');
    Route::post('/update/homecategory/image', [SiteMetaController::class, 'updateCategoryImage'])->name('admin.dashboard.update_homecategory_image');
    Route::get('/admin-dashboard/web-setting', [SiteMetaController::class, 'webSetting'])->name('admin.dashboard.web_setting');
    Route::post('/admin-dashboard/add/web-setting', [SiteMetaController::class, 'addWebsetting'])->name('admin.dashboard.add_web_setting');
    Route::post('/admin-dashboard/update/web-setting', [SiteMetaController::class, 'updateWebsetting'])->name('admin.dashboard.update_web_setting');
    Route::get('/admin-dashboard/logos', [SiteMetaController::class, 'logos'])->name('admin.dashboard.logos');
    Route::post('/admin-dashboard/add/logos', [SiteMetaController::class, 'addLogos'])->name('admin.dashboard.add_logos');
    Route::get('/admin-dashboard/messages', [AdminController::class, 'messages'])->name('admin.dashboard.messages');
    Route::post('/admin-dashboard/save/messages', [AdminController::class, 'saveMesage'])->name('admin.dashboard.save_messages');
    Route::get('/admin-dashboard/legal-document', [SiteMetaController::class, 'legal_document'])->name('admin.dashboard.legal_document');
    Route::post('/admin-dashboard/add-legal-document', [SiteMetaController::class, 'addLegal'])->name('admin.dashboard.add_legal_document');
    Route::get('/admin-dashboard/header', [MetaDataController::class, 'header'])->name('admin.dashboard.header');
    Route::post('/admin-dashboard/add/header', [MetaDataController::class, 'addHeader'])->name('admin.dashboard.add_header');
    Route::get('/admin-dashboard/footer', [MetaDataController::class, 'footer'])->name('admin.dashboard.footer');
    Route::post('/admin-dashboard/add/footer', [MetaDataController::class, 'addFooter'])->name('admin.dashboard.add_footer');

    Route::get('/admin-dashboard/add/configuration', [GlobalController::class, 'configuration'])->name('admin.global.configuration');
    Route::post('/admin-dashboard/add/configuration/process', [GlobalController::class, 'addGlobalConfiguration'])->name('admin.add.global.configuration');

    Route::get('/admin-dashboard/product', [ProductController::class, 'product'])->name('admin.dashboard.product');
    Route::post('/admin-dashboard/add-product', [ProductController::class, 'addProduct'])->name('admin.dashboard.add_product');
    Route::get('/admin-dashboard/all-products', [ProductController::class, 'allproducts'])->name('admin.dashboard.all_products');
    Route::get('/admin-dashboard/product/{id}', [ProductController::class, 'editProduct'])->name('admin.dashboard.edit_products');
    Route::get('/admin-dashboard/product-categories', [ProductController::class, 'productCategory'])->name('admin.dashboard.product_categories');
    Route::post('/admin-dashboard/add-categories', [ProductController::class, 'addProductCategory'])->name('admin.dashboard.add_categories');
    Route::get('/admin-dashboard/categories', [ProductController::class, 'categories'])->name('admin.dashboard.categories');
    Route::get('/admin-dashboard/product-categories/{slug}', [ProductController::class, 'editCategories'])->name('admin.dashboard.edit_product_categories');

    Route::get('/admin-dashboard/users', [AllPagesController::class, 'allUsers'])->name('all.users');
    Route::get('/admin-dashboard/users/add', [AllPagesController::class, 'EditUser'])->name('add.user');
    Route::post('/admin-dashboard/update-user', [AllPagesController::class, 'updateUser'])->name('update.user');
    Route::get('/admin-dashboard/delete-user/{id}', [AllPagesController::class, 'deleteUser'])->name('delete.user');
    Route::get('/admin-dashboard/orders', [AllPagesController::class, 'orders'])->name('admin.orders');
    Route::get('/admin-dashboard/orders-details/{id}', [AllPagesController::class, 'ordersDetail'])->name('orders.details');
    Route::post('/admin-dashboard/orders-details/update/{id}', [AllPagesController::class, 'updateOrdersDetail'])->name('update.orders.details');
    Route::get('/admin-dashboard/subscription/plans', [AdminController::class, 'plans'])->name('admin.subscription.plans');
    Route::get('/admin-dashboard/subscription/plans/add', [AdminController::class, 'subscriptionPlans'])->name('admin.add.plans');
    Route::post('/admin-dashboard/add-subscription-plan', [AdminController::class, 'addSubscriptionPlan'])->name('admin.add.subscription.plan');
    Route::get('/admin-dashboard/delete/plan', [AdminController::class, 'deleteSubscriptionPlan'])->name('admin.delete.plan');
    Route::get('/admin-dashboard/discount', [AdminController::class, 'allDiscount'])->name('admin.discount');
    Route::get('/admin-dashboard/add/discount', [AdminController::class, 'addDiscount'])->name('admin.add.discount');
    Route::post('/admin-dashboard/discount/addProcc', [AdminController::class, 'addDiscountProcc'])->name('admin.add.discount.process');
    Route::post('/admin-dashboard/update/customer-information/{id}', [AllPagesController::class, 'updateCustomerInformation'])->name('update.customer.details');
    Route::post('/admin-dashboard/update/billing-details/{id}', [AllPagesController::class, 'updateBillingDetails'])->name('update.billing.details');
    Route::get('/admin/orders/{id}/invoice/content', [AllPagesController::class, 'print'])->name('admin.order.invoice.content');
    Route::get('/admin/orders/{id}/download-invoice', [AllPagesController::class, 'downloadInvoice'])->name('admin.order.invoice.download');
    Route::get('/admin-dashboard/orders-details/all-orders/{id}', [AllPagesController::class, 'showAllOrder'])->name('show.orders');
    Route::get('/admin-dashboard/orders/add-orders', [AllPagesController::class, 'addOrder'])->name('add.orders');
    Route::post('/admin-dashboard/save/free-grant-document', [AllPagesController::class, 'saveFreeGrantDocument'])->name('save.free.grantDocument');
    Route::post('/admin-dashboard/save/free-subscription', [AllPagesController::class, 'addSubscriptionToOrder'])->name('save.free.subscription');

    Route::post('admin-dashboard/knowledge-base/classicEditor/upload-image', [KnowledgeBaseController::class, 'uploadEditorImage'])->name('knowledge.base.upload.editor.image');
    Route::post('admin-dashboard/knowledge-base/classicEditor/upload-image-base64', [KnowledgeBaseController::class, 'uploadEditorImageBase24'])->name('knowledge.base.upload.editor.image.base62');
    Route::get('/admin-dashboard/knowledge-base/category', [KnowledgeBaseController::class, 'knowledge_base_categories'])->name('knowledge.base.category');
    Route::get('/admin-dashboard/knowledge-base/article', [KnowledgeBaseController::class, 'knowledge_base_article'])->name('knowledge.base.article');
    Route::get('/admin-dashboard/knowledge-base/add-category', [KnowledgeBaseController::class, 'addCategory'])->name('knowledge.base.add.category');
    Route::post('/admin-dashboard/knowledge-base/add-category', [KnowledgeBaseController::class, 'storeCategory'])->name('knowledge.base.store.category');
    Route::get('/admin-dashboard/knowledge-base/edit-category/{id}', [KnowledgeBaseController::class, 'editCategory'])->name('knowledge.base.edit.category');
    Route::post('/admin-dashboard/knowledge-base/update-category', [KnowledgeBaseController::class, 'updateCategory'])->name('knowledge.base.update.category');
    Route::post('/admin-dashboard/knowledge-base/delete-category-image', [KnowledgeBaseController::class, 'deleteCategoryImage'])->name('knowledge.base.delete.category.image');
    Route::post('/admin-dashboard/knowledge-base/delete-category', [KnowledgeBaseController::class, 'deleteCategory'])->name('knowledge.base.delete.category');
    Route::get('/admin-dashboard/knowledge-base/add-article', [KnowledgeBaseController::class, 'addArticle'])->name('knowledge.base.add.article');
    Route::post('/admin-dashboard/knowledge-base/add-article', [KnowledgeBaseController::class, 'storeArticle'])->name('knowledge.base.store.article');
    Route::get('/admin-dashboard/knowledge-base/edit-article/{id}', [KnowledgeBaseController::class, 'editArticle'])->name('knowledge.base.edit.article');
    Route::post('/admin-dashboard/knowledge-base/update-article', [KnowledgeBaseController::class, 'updatearticle'])->name('knowledge.base.update.article');
    Route::post('/admin-dashboard/knowledge-base/delete-article-image', [KnowledgeBaseController::class, 'deleteArticleImage'])->name('knowledge.base.delete.article.image');
    Route::post('/admin-dashboard/knowledge-base/delete-article', [KnowledgeBaseController::class, 'deleteArticle'])->name('knowledge.base.delete.article');
    Route::get('/admin-dashboard/test-svg', [ImageTextController::class, 'generateSvg'])->name('admin.dashboard.generateSvg');

    Route::get('/admin-dashboard/all-prompt', [AiPromptController::class, 'allPrompt'])->name('all.prompt');
    Route::get('/admin-dashboard/add-prompt', [AiPromptController::class, 'addPrompt'])->name('add.prompt');
    Route::get('/admin-dashboard/documents-prompts', [AiPromptController::class, 'documentPrompts'])->name('admin.docx_prompts');
    Route::get('/admin-dashboard/prompts/config', [AiPromptController::class, 'config'])->name('ai.config');
    Route::get('/admin-dashboard/prompts/get-info', [AiPromptController::class, 'getInfo'])->name('ai.prompt.get_info');
    Route::post('/admin-dashboard/prompts/configUpdate', [AiPromptController::class, 'configupdate'])->name('ai.config.update');
    Route::get('/admin-dashboard/prompts/config/{modelRef}', [AiPromptController::class, 'deleteConfigByModelRef'])->name('ai.config.delete');
    Route::post('/admin-dashboard/documents-prompts', [AiPromptController::class, 'saveDocumentPrompt'])->name('save.prompt.selection');
    Route::post('/admin-dashboard/add-prompt-procc', [AiPromptController::class, 'storePrompt'])->name('store.prompt');
    Route::get('/admin-dashboard/edit-prompt/{id}', [AiPromptController::class, 'editPrompt'])->name('edit.prompt');
    Route::post('/admin-dashboard/edit-prompt', [AiPromptController::class, 'updatePrompt'])->name('update.prompt');
    Route::post('/admin-dashboard/delete-prompt/{id}', [AiPromptController::class, 'deletePrompt'])->name('delete.prompt');
    Route::get('/admin-dashboard/prompt-verification', [AiPromptController::class, 'aiVerification'])->name('verification.prompt');
    Route::post('/admin-dashboard/verificationAddprocc', [AiPromptController::class, 'verificationAddProcess'])->name('prompt.verification.add');
    Route::get('/admin-dashboard/document-generating-prompts', [DocumentGeneratingPromptController::class, 'documentGeneratingPrompt'])->name('doc.prompts');
    Route::get('/admin-dashboard/document-generating-prompts/get', [DocumentGeneratingPromptController::class, 'getPrompts'])->name('get.document.prompts');
    Route::post('/admin-dashboard/document-generating-prompts/store', [DocumentGeneratingPromptController::class, 'store'])->name('store.document.prompts');
    Route::put('/admin-dashboard/document-generating-prompts/update', [DocumentGeneratingPromptController::class, 'update'])->name('update.document.prompts');
    Route::delete('/admin-dashboard/document-generating-prompts/delete', [DocumentGeneratingPromptController::class, 'destroy'])->name('delete.document.prompts');

    Route::get('/admin-dashboard/support', [AdminController::class, 'support'])->name('admin.dashboard.support');
    Route::get('/admin-dashboard/support/{id}', [AdminController::class, 'supportView'])->name('admin.dashboard.support.view');
    Route::post('/admin-dashboard/support/{id}', [AdminController::class, 'adminReply'])->name('admin.ticket.reply');
    Route::post('/admin/tickets/{ticket}/toggle', [AdminController::class, 'toggleStatus'])->name('admin.tickets.toggle');

    Route::get('/admin-dashboard/ai-FAQ', [AdminController::class, 'AiFAQ'])->name('admin.dashboard.ai.FAQ');
    Route::get('admin/dashboard/store/ai/FAQ/{id?}', [AdminController::class, 'AddAIFaq'])->name('admin.dashboard.add.ai.FAQ');
    Route::post('/admin-dashboard/ai-FAQ', [AdminController::class, 'StoreAIFaq'])->name('admin.dashboard.store.ai.FAQ');
    Route::get('faq/destroy/{id}', [AdminController::class, 'destroyAIFaq'])->name('admin.dashboard.ai.FAQ.destroy');
    Route::get('/admin-dashboard/ai/pending/FAQ', [AdminController::class, 'getPendingFaq'])->name('admin.dashboard.ai.pending.FAQ');
    Route::get('/admin/dashboard/answer/ai/FAQ/{id}', [AdminController::class, 'answerAiFaq'])->name('admin.dashboard.answer.ai.FAQ');
    Route::post('/admin-dashboard/ai-FAQ/answer', [AdminController::class, 'StoreAIFaqAnswer'])->name('admin.dashboard.store.ai.FAQ.answer');
    Route::get('pending-faq/destroy/{id}', [AdminController::class, 'destroyPendingFaq'])->name('admin.dashboard.ai.pending.FAQ.destroy');
    Route::get('/admin-dashboard/ai-tags', [AdminController::class, 'AiTag'])->name('admin.dashboard.ai.FAQ.tags');
    Route::get('/admin/dashboard/store/ai/tags/{id?}', [AdminController::class, 'AddAiTags'])->name('admin.dashboard.add.ai.tag');
    Route::post('/admin-dashboard/ai-tag', [AdminController::class, 'StoreAiTag'])->name('admin.dashboard.store.ai.tag');
    Route::get('tag/destroy/{id}', [AdminController::class, 'destroyAiTag'])->name('admin.dashboard.ai.tag.destroy');
    Route::get('/admin-dashboard/recovery-password-mail/{type}', [AdminController::class, 'recoveryPassword'])->name('admin.dashboard.recovery.password.email');
    Route::post('/admin-dashboard/recovery-password-mail/save', [AdminController::class, 'storeRecoveryPassword'])->name('admin.dashboard.store.recovery.password.email');
    Route::get('/admin-dashboard/change-password', [AdminController::class, 'adminChnagePassword'])->name('admin.dashboard.change.password');
    Route::post('/admin-dashboard/change-password/save', [AdminController::class, 'storeAdminChnagePassword'])->name('admin.dashboard.store.change.password');
});

Route::get('/admin-dashboard/state-specific-clauses', [StateSpecificClauseController::class, 'index'])->name('admin-dashboard.state-specific-clauses');
Route::get('admin-dashboard/state-specific-clauses', [StateSpecificClauseController::class, 'index'])->name('index');
Route::get('admin/state-clauses/create', [StateSpecificClauseController::class, 'create'])->name('admin.state-clauses.create');
Route::post('/', [StateSpecificClauseController::class, 'store'])->name('admin.state-clauses.store');
Route::get('/{id}/edit', [StateSpecificClauseController::class, 'edit'])->name('edit');
Route::put('/{id}', [StateSpecificClauseController::class, 'update'])->name('update');
Route::delete('/admin/state-clauses/{id}', [StateSpecificClauseController::class, 'destroy'])->name('destroy');
Route::patch('/{id}/toggle', [StateSpecificClauseController::class, 'toggleActive'])->name('toggle');
Route::delete('/state-clauses/delete-all', [StateSpecificClauseController::class, 'destroyAll'])->name('state-clauses.destroyAll');
Route::get('/by-state', [StateSpecificClauseController::class, 'getByState'])->name('by-state');
Route::get('/for-ai', [StateSpecificClauseController::class, 'getForAI'])->name('for-ai');
Route::post('/ai-auto-fill', [StateSpecificClauseController::class, 'aiAutoFill'])->name('state-clauses.ai-auto-fill');

Route::prefix('admin-dashboard')->middleware(['admin'])->group(function () {
    Route::get('/parties-template', [PartiesSectionTemplateController::class, 'index'])->name('admin.parties-templates');
    Route::get('/parties-template/create', [PartiesSectionTemplateController::class, 'create'])->name('admin.parties-templates.create');
    Route::post('parties-template', [PartiesSectionTemplateController::class, 'store'])->name('admin.parties-templates.store');
    Route::get('/parties-template/{partiesTemplate}/edit', [PartiesSectionTemplateController::class, 'edit'])->name('admin.parties_templates.edit');
    Route::put('/parties-template/{partiesTemplate}', [PartiesSectionTemplateController::class, 'update'])->name('admin.parties-templates.update');
    Route::delete('/parties-template/{partiesTemplate}', [PartiesSectionTemplateController::class, 'destroy'])->name('admin.parties-templates.destroy');
    Route::get('/document-generator/parties-templates', [DocumentBetaController::class, 'getPartiesTemplates']);
});

Route::post('/admin-dashboard/api/ai-autofill', [DocumentBetaController::class, 'aiAutofill'])->name('admin.document.ai-autofill');
Route::get('/admin-dashboard/api/ce-questions/{id}', [DocumentController::class, 'ceGetQuestions']);
Route::get('/admin-dashboard/api/ce-sections/{id}', [DocumentController::class, 'ceGetSections']);
Route::post('/admin-dashboard/api/ce-save', [DocumentController::class, 'ceSave']);
Route::post('admin-dashboard/api/ce-ai-edit', [DocumentController::class, 'ceAiEdit'])->name('admin.ce.ai_edit');
Route::get('/admin-dashboard/api/standard-documents', [DocumentController::class, 'ceGetStandardDocuments']);
Route::get('/admin-dashboard/api/standard-document-detail/{id}', [DocumentController::class, 'ceGetStandardDocumentDetail']);
