import React, { useReducer } from 'react';
import PropTypes from 'prop-types';
import classnames from 'classnames/bind';

import { useInterval } from 'hooks';

import Nav from './Nav';
import styles from './styles.css';

const cx = classnames.bind(styles);

const Banners = ({ className, children, interval, autoPlay: autoPlayProp }) => {
    const [state, dispatch] = useReducer(
        (prevState, action) => {
            const childLength = children.length;

            switch (action.type) {
                case 'NEXT':
                    return {
                        ...prevState,
                        active: (prevState.active + 1) % childLength,
                    };
                case 'PREV':
                    return {
                        ...prevState,
                        active: (prevState.active - 1 + childLength) % childLength,
                    };
                case 'PAUSE':
                    return {
                        ...prevState,
                        isPlaying: false,
                    };
                case 'PLAY':
                    return {
                        ...prevState,
                        isPlaying: true,
                    };
                default:
                    return prevState;
            }
        },
        {
            active: 0,
            isPlaying: autoPlayProp,
        }
    );
    useInterval(
        () => {
            dispatch({ type: 'NEXT' });
        },
        state.isPlaying ? interval : null
    );
    const handleChange = index => {
        dispatch({ type: index > state.active ? 'NEXT' : 'PREV' });
    };
    const handleHover = ({ type }) => {
        const events = {
            mouseenter: 'PAUSE',
            mouseleave: 'PLAY',
        };

        dispatch({ type: events[type] });
    };
    const getChildrens = React.Children.map(children, (child, index) => {
        if (!React.isValidElement(child)) {
            return null;
        }

        const activeBannerClassName = cx(styles.item, {
            active: state.active === index,
        });

        return (
            <div
                key={index} // eslint-disable-line
                className={activeBannerClassName}
            >
                {child}
            </div>
        );
    });

    const rootClassName = cx(styles.wrapper, className);

    return (
        <div
            className={rootClassName}
            onMouseEnter={autoPlayProp ? handleHover : null}
            onMouseLeave={autoPlayProp ? handleHover : null}
        >
            <div className={styles.items}>{getChildrens}</div>
            {children.length > 1 && <Nav index={state.active} onChange={handleChange} />}
        </div>
    );
};

Banners.defaultProps = {
    children: [],
    interval: 10000,
    autoPlay: false,
};
Banners.propTypes = {
    children: PropTypes.node,
    interval: PropTypes.number,
    autoPlay: PropTypes.bool,
};

export default Banners;
