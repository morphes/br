import React from 'react';
import { Helmet } from 'react-helmet';
import { useHistory } from 'react-router';
import { Link } from 'react-router-dom';
import { FormattedMessage } from 'react-intl';

import { useLangLinks, useApp, useFormatMessage } from 'hooks';

import Title from 'components/Title';
import Button from 'components/Button';

import CardContent from './CardContent';
import styles from './styles.css';

import FavoritesIcon from './icons/favorites.svg';
import ProfileIcon from './icons/profile.svg';
import PrescriptionsIcon from './icons/prescriptions.svg';
import AddressesIcon from './icons/addresses.svg';

const User = () => {
    const [metaTitle] = useFormatMessage([{ id: 'account' }]);
    const history = useHistory();
    const { logout } = useApp();
    const [favoritesLink, prescriptionsLink, addressesLink, profileLink] = useLangLinks([
        '/account/favorites',
        '/account/prescriptions',
        '/account/addresses',
        '/account/profile',
    ]);

    const handleLogoOut = () => {
        logout();
        history.push('/');
    };

    return (
        <div className={styles.root}>
            <Helmet title={metaTitle} />
            <div className={styles.container}>
                <div className={styles.header}>
                    <Title className={styles.title}>
                        <FormattedMessage id="account" />
                    </Title>
                </div>
                <section className={styles.cards}>
                    {/* <Link to={favoritesLink} className={styles.card}>
                        <CardContent
                            title={<FormattedMessage id="favorites" />}
                            icon={<FavoritesIcon />}
                            text={<FormattedMessage id="favorites_text" />}
                        />
                    </Link> */}
                    {/*<Link to={prescriptionsLink} className={styles.card}>
                        <CardContent
                            title={<FormattedMessage id="prescriptions" />}
                            icon={<PrescriptionsIcon />}
                            text={<FormattedMessage id="prescriptions_text" />}
                        />
                    </Link> */}
                    <Link to={addressesLink} className={styles.card}>
                        <CardContent
                            title={<FormattedMessage id="addresses" />}
                            icon={<AddressesIcon />}
                            text={<FormattedMessage id="addresses_text" />}
                        />
                    </Link>
                    <Link to={profileLink} className={styles.card}>
                        <CardContent
                            title={<FormattedMessage id="profile" />}
                            icon={<ProfileIcon />}
                            text={<FormattedMessage id="profile_text" />}
                        />
                    </Link>
                </section>
                <p className={styles.footer}>
                    <Button onClick={handleLogoOut} kind="simple" bold>
                        <FormattedMessage id="log_out" />
                    </Button>
                </p>
            </div>
        </div>
    );
};

export default User;
