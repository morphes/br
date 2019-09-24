import React, { useState } from 'react';
import PropTypes from 'prop-types';
import classnames from 'classnames/bind';
import { FormattedMessage } from 'react-intl';
import { useMutation } from '@apollo/react-hooks';

import { SeoHead } from 'utils';
import { useLangLink, useApp } from 'hooks';
import { ADD_TO_BASKET } from 'mutations';
import { GET_SHORT_BASKET } from 'query';

import Button from 'components/Button';
import HeadTurn from 'components/HeadTurn';
import Delivery from 'components/Delivery';
import Colors from 'components/Colors';
import Container from 'components/Container';
import ProductCarousel from 'components/ProductCarousel';
import ProductCard from 'components/ProductCard';

import styles from './styles.css';
import headTurnImage from './headturn.jpg';
import ChooseLenses from './ChooseLenses';

const cx = classnames.bind(styles);

const Product = ({ name, items: { edges: items = [] }, tags, similars }) => {
    const buyLink = useLangLink('/retail');
    const { createNotification } = useApp();
    const [selectedProduct, setSelectedProduct] = useState(items.length ? items[0].node : {});
    const images = selectedProduct.productItemImages;
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
    const [showChooseLenses, setShowChooseLenses] = useState(false);
    const [addToCard, { data: addBasket, loadingMutation }] = useMutation(ADD_TO_BASKET, {
        variables: {
            input: {
                item_id: selectedProduct.id,
            },
        },
        onCompleted({ addBasket: { products } }) {
            if (products) {
                console.info('product added to basket', products);
                history.push('/cart');
            }
        },
        onError(error) {
            createNotification({ type: 'error', message: error.message });
        },
        // TODO
        update(
            cache,
            {
                data: { addBasket },
            }
        ) {
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
    const handleShowCL = () => {
        setShowChooseLenses(!showChooseLenses);
    };

    const sectionTitleCenterClassName = cx(styles.sectionTitle, styles.center);
    const rootClassName = cx(styles.root, { hide: showChooseLenses });

    return (
        <Container>
            <SeoHead type="product" name={name} items={items} image={images ? images[0].path : null} />
            {/*showChooseLenses && <ChooseLenses title={name} onClose={handleShowCL} />*/}
            <div className={rootClassName}>
                {images.length ? (
                    <div className={styles.carouselWrapper}>
                        <ProductCarousel items={images} />
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
                    <div className={styles.info}>
                        <FormattedMessage id="product_text" values={{ br: <br /> }} />
                    </div>
                    {selectedProduct.price && (
                        <div className={styles.buttons}>
                            <Button to={buyLink} kind="primary" size="large" bold>
                                <FormattedMessage id="buy_at_optics_for" /> {selectedProduct.price} руб.
                            </Button>
                            <Button onClick={handleShowCL} kind="primary" size="large" bold>
                                Select lenses and purchase
                            </Button>
                        </div>
                    )}
                </div>
            </div>
            <div className={styles.section}>
                <HeadTurn
                    images={[headTurnImage]}
                    title={<FormattedMessage id="about_the_frames" />}
                    text={
                        tags.length ? (
                            <>
                                <ul>
                                    {tags.map((tag, index) => {
                                        return (
                                            <li key={index}>
                                                {tag.name}: {tag.value}
                                            </li>
                                        );
                                    })}
                                </ul>
                            </>
                        ) : null
                    }
                />
            </div>
            <Delivery
                title={<FormattedMessage id="delivery_block_title" />}
                text={<FormattedMessage id="delivery_block_text" />}
            />
            {similars.length ? (
                <div className={styles.section}>
                    <h2 className={sectionTitleCenterClassName}>
                        <FormattedMessage id="recommended" />
                    </h2>
                    <div className={styles.related}>
                        {similars.map(item => (
                            <div key={item.id} className={styles.relatedProduct}>
                                <ProductCard name={item.name} url={item.url} image={item.image} />
                            </div>
                        ))}
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
};

Product.propTypes = {
    name: PropTypes.string,
    id: PropTypes.number.isRequired,
    items: PropTypes.shape({
        edges: PropTypes.arrayOf(PropTypes.object),
    }),
    tags: PropTypes.arrayOf(PropTypes.object),
};

export default Product;
