export const addProductLabels = productLabels => {
    CouponsPlus.products.labels = {
        ...CouponsPlus.products.labels,
        ...productLabels
    };
}

export const getProductNameById = productId => {
    return CouponsPlus.products.labels[productId] || productId;
}

export const addProductsWithIds = idsWithVariations => {
    for (let {id, variationIDs} of idsWithVariations.slice()) {
        CouponsPlus.products.idsWithVariations = {
            ...(Array.isArray(CouponsPlus.products.idsWithVariations)? {} : CouponsPlus.products.idsWithVariations),
            ...{[id]: variationIDs}
        };
    }
}

export const getVariationIdsFromProductIds = productId => {
    return CouponsPlus.products.idsWithVariations[productId] || [];
}

