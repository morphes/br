import React from 'react';
import { FormattedMessage } from 'react-intl';

import Container from 'components/Container';
import Title from 'components/Title';

import styles from './styles.css';

const ErrorBoundary = () => (
    <Container>
        <div className={styles.text}>
            <Title>
                <FormattedMessage id="p_500_title" /> 😢
            </Title>
        </div>
    </Container>
);

export default ErrorBoundary;
