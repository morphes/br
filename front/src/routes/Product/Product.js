import React, { useState } from 'react';
import PropTypes from 'prop-types';
import classnames from 'classnames/bind';
import { FormattedMessage } from 'react-intl';
import { useMutation } from '@apollo/react-hooks';
import { useHistory } from 'react-router';

import { ADD_TO_BASKET } from 'mutations';
import { GET_SHORT_BASKET, GET_BASKET } from 'query';
import { useApp, useLangLinks, useLang } from 'hooks';
import { metrics } from 'utils';
import LANGS from 'lang';

import SeoHead from 'components/SeoHead';
import Button from 'components/Button';
import Colors from 'components/Colors';
import Container from 'components/Container';
import Delivery from 'components/Delivery';
import ProductTags from 'components/ProductTags';
import ProductCard from 'components/ProductCard';
import ProductCarousel from 'components/ProductCarousel';
import Loader from 'components/Loader';
import LiteraryCallout from 'components/LiteraryCallout';

import styles from './styles.css';
import caseAndLensClothImage from './images/case-and-lens-cloth.jpg';
import caseAndLensClothImageRetina from './images/case-and-lens-cloth@2x.jpg';
import caseAndLensClothImageWebp from './images/case-and-lens-cloth.webp';
import caseAndLensClothImageWebpRetina from './images/case-and-lens-cloth@2x.webp';
import { ChooseLensesPortal } from './ChooseLenses';

const cx = classnames.bind(styles);

const Product = ({
    name,
    sku,
    google_title: googleTitle,
    google_description: googleDescription,
    items: { edges: items = [] },
    tags,
    similars: { edges: similars = [] },
    lenses,
}) => {
    const lang = useLang();
    const history = useHistory();
    const { createNotification } = useApp();
    const [buyLink] = useLangLinks(['/retail']);
    const [selectedProduct, setSelectedProduct] = useState(items.length ? items[0].node : {});
    const [showChooseLenses, setShowChooseLenses] = useState(false);

    const [addToCart, { loading: loadingAddToCart }] = useMutation(ADD_TO_BASKET, {
        variables: {
            input: {
                item_id: selectedProduct.id,
            },
        },
        onCompleted({ addBasket: { products } }) {
            if (products) {
                console.log('product added to basket', products);
                metrics('gtm', {
                    event: 'addToCart',
                    data: {
                        currencyCode: 'RUB',
                        add: {
                            products: [
                                {
                                    name,
                                    id: selectedProduct.id,
                                    price: selectedProduct.price,
                                    variant: selectedProduct.name,
                                    quantity: 1,
                                },
                            ],
                        },
                    },
                });

                const defaultLang = LANGS.find(item => item.default);
                history.push(defaultLang.value === lang ? '/cart' : `/${lang}/cart`);
            }
        },
        onError({ graphQLErrors: [{ message }] }) {
            createNotification({ type: 'error', message });
        },
        update(cache, { data: { addBasket } }) {
            /* <3 apollo */
            cache.writeQuery({
                query: GET_SHORT_BASKET,
                data: {
                    basket: {
                        products: addBasket.products,
                        __typename: 'Basket',
                    },
                },
            });
        },
    });

    const { images } = selectedProduct;
    const colors = items.reduce((array, item) => {
        const [{ image }] = item.node.productItemTagItems;

        return array.concat({ id: item.node.id, image });
    }, []);

    const handleChangeColor = id => {
        const newSelectedProduct = items.find(item => item.node.id === id);

        if (newSelectedProduct && newSelectedProduct.node.id !== selectedProduct.id) {
            setSelectedProduct(newSelectedProduct.node);
        }
    };
    const handleCloseChooseLenses = () => {
        setShowChooseLenses(!showChooseLenses);
    };

    const sectionTags = cx(styles.section, styles.tags);

    return (
        <Container>
            <SeoHead
                type="product"
                og={{ title: googleTitle, description: googleDescription }}
                name={name}
                items={items}
                image={images.length ? images[0].path : null}
            />
            {showChooseLenses && (
                <ChooseLensesPortal
                    product={{ name, item: selectedProduct }}
                    lenses={lenses}
                    loading={loadingAddToCart}
                    onAddToCart={addToCart}
                    onClose={handleCloseChooseLenses}
                />
            )}
            <div className={styles.productDetails}>
                {images.length ? (
                    <div className={styles.carouselWrapper}>
                        <ProductCarousel>
                            {images.map((image, index) => (
                                <picture key={index}>
                                    <source
                                        srcSet={`${image.middle.webp} 1x, ${image.big.webp} 2x`}
                                        type="image/webp"
                                    />
                                    <img
                                        src={image.middle.original}
                                        srcSet={`${image.big.original} 2x`}
                                        alt=""
                                    />
                                </picture>
                            ))}
                        </ProductCarousel>
                    </div>
                ) : null}
                <div className={styles.meta}>
                    <h1 className={styles.title}>{name}</h1>
                    <h2 className={styles.description}>{selectedProduct.name}</h2>
                    <div className={styles.colorsWrapper}>
                        <Colors
                            value={selectedProduct.id}
                            list={colors}
                            onChange={value => handleChangeColor(value)}
                        />
                    </div>
                    <div className={styles.price}>
                        <FormattedMessage id="p_product_price" values={{ price: selectedProduct.price }} />
                    </div>
                    {selectedProduct.price && (
                        <div className={styles.buttons}>
                            {lenses.length ? (
                                <Button
                                    onClick={() => setShowChooseLenses(true)}
                                    kind="primary"
                                    size="large"
                                    bold
                                >
                                    <FormattedMessage id="p_product_select_lenses" />
                                </Button>
                            ) : (
                                <Button onClick={addToCart} kind="primary" size="large" bold>
                                    {loadingAddToCart ? (
                                        <Loader kind="secondary" />
                                    ) : (
                                        <FormattedMessage id="p_product_add_to_cart" />
                                    )}
                                </Button>
                            )}
                            <Button to={buyLink} kind="primary" size="large" bold>
                                <FormattedMessage id="p_product_buy_at_optics_for" />
                            </Button>
                        </div>
                    )}
                </div>
            </div>
            <div className={sectionTags}>
                <ProductTags
                    items={[{ name: <FormattedMessage id="p_product_tags_sku" />, value: sku }, ...tags]}
                    image={images[1] ? images[1] : null}
                />
            </div>
            <Delivery
                title={<FormattedMessage id="p_product_lenses_block_title" />}
                text={<FormattedMessage id="p_product_lenses_block_text" />}
            />
            <LiteraryCallout
                image={{
                    root: { original: caseAndLensClothImage, retina: caseAndLensClothImageRetina },
                    webp: { original: caseAndLensClothImageWebp, retina: caseAndLensClothImageWebpRetina },
                }}
                text={<FormattedMessage id="p_product_delivery_text" />}
            />
            {items.length > 1 ? (
                <div className={styles.section}>
                    <h2 className={styles.sectionTitle}>
                        <FormattedMessage id="p_product_section_colors_title" />
                    </h2>
                    <div className={styles.related}>
                        {items.map(({ node: product }) => (
                            <div key={product.id} className={styles.relatedProduct}>
                                <ProductCard
                                    name={product.name}
                                    url={product.url}
                                    image={product.images[0] ? product.images[0] : null}
                                    onClick={() => {
                                        window.scrollTo(0, 0);
                                        handleChangeColor(product.id);
                                    }}
                                />
                            </div>
                        ))}
                    </div>
                </div>
            ) : null}
            {similars.length ? (
                <div className={styles.section}>
                    <h2 className={styles.sectionTitle}>
                        <FormattedMessage id="recommended" />
                    </h2>
                    <div className={styles.related}>
                        {similars.map(item => {
                            const [{ node: firstItem }] = item.node.items.edges;

                            return (
                                <div key={item.node.id} className={styles.relatedProduct}>
                                    <ProductCard
                                        name={item.node.name}
                                        url={item.node.url}
                                        subname={firstItem.name}
                                        image={firstItem.images[0] ? firstItem.images[0] : null}
                                    />
                                </div>
                            );
                        })}
                    </div>
                </div>
            ) : null}
        </Container>
    );
};

Product.defaultProps = {
    items: [],
    name: 'Без названия',
    tags: [],
    similars: [],
    lenses: [],
};

Product.propTypes = {
    name: PropTypes.string,
    // id: PropTypes.number.isRequired,
    lenses: PropTypes.arrayOf(PropTypes.object),
    items: PropTypes.shape({
        edges: PropTypes.arrayOf(PropTypes.object),
    }),
    similars: PropTypes.objectOf(PropTypes.array),
    tags: PropTypes.arrayOf(PropTypes.object),
};

export default Product;
