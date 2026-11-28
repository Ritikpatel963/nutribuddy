<?php

namespace App\Http\Requests\Admin\Ecommerce;

class StoreProductRequest extends ProductMutationRequest
{
    public function rules(): array
    {
        return $this->productRules()
            + $this->transformSectionRules()
            + $this->featuresSectionRules()
            + $this->formulaSectionRules()
            + $this->faqSectionRules()
            + $this->comboRules()
            + $this->subscriptionRules();
    }
}
