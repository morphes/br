import React, { Component } from 'react';

import MyErrorBoundary from 'components/ErrorBoundary';

class ErrorBoundary extends Component {
    state = {
        hasError: false,
    };

    static getDerivedStateFromError(error) {
        // Update state so the next render will show the fallback UI.
        return {
            hasError: true,
        };
    }

    componentDidCatch(error, errorInfo) {
        // You can also log the error to an error reporting service
        // logErrorToMyService(error, errorInfo);
    }

    render() {
        const { fallback, children } = this.props;

        if (this.state.hasError) {
            return fallback;
        }

        return children;
    }
}

const withErrorBoundary = RootComponent => (props = {}) => (
    <ErrorBoundary fallback={<MyErrorBoundary />}>
        <RootComponent {...props} />
    </ErrorBoundary>
);

export default withErrorBoundary;
