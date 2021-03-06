import React, { useState, useEffect, useRef } from 'react';
import { useMutation } from '@apollo/react-hooks';
import { FormattedMessage } from 'react-intl';
import { Edit as EditIcon, X as RemoveIcon } from 'react-feather';

import { REMOVE_ADDRESS_MUTATION } from 'mutations';
import { GET_ADDRESSES } from 'query';

import Title from 'components/Title';
import ListItem from 'components/ListItem';
import Button from 'components/Button';
import AddressForm from 'components/AddressForm';
import Container from 'components/Container';

import styles from './styles.css';

const Addresses = ({ items = [], onSubmit }) => {
    const formEl = useRef(null);
    const [showForm, setShowForm] = useState(null);
    const [handleRemoveAddress] = useMutation(REMOVE_ADDRESS_MUTATION, {
        update(
            cache,
            {
                data: { removeAddress },
            }
        ) {
            cache.writeQuery({
                query: GET_ADDRESSES,
                data: {
                    addresses: {
                        data: removeAddress.data,
                        __typename: 'Addresses',
                    },
                },
            });
        },
    });
    const handleSubmitAddress = data => {
        // if edit take new addresses from data.data
        // esle add new address from data to items
        setShowForm(null);
        if (onSubmit) onSubmit(data.data ? data.data : data);
    };

    useEffect(() => {
        if (formEl.current) {
            formEl.current.scrollIntoView();
        }
    }, [showForm]);

    if (showForm) {
        return (
            <Container size="form" className={styles.root} ref={formEl}>
                <h1 className={styles.formTitle}>
                    <FormattedMessage id={showForm.id ? 'p_addresses_edit_title' : 'p_addresses_new_title'} />
                </h1>
                <AddressForm
                    id={showForm.id}
                    actions={
                        <Button
                            kind="secondary"
                            bold
                            onClick={() => {
                                setShowForm(null);
                            }}
                        >
                            <FormattedMessage id="back" />
                        </Button>
                    }
                    onSubmit={handleSubmitAddress}
                />
            </Container>
        );
    }

    return (
        <Container size="form" className={styles.root}>
            <div className={styles.header}>
                <Title className={styles.title}>
                    <FormattedMessage id="p_addresses_title" />
                </Title>
            </div>
            {items && items.length ? (
                items.map(item => (
                    <ListItem
                        key={item.id}
                        title={item.name}
                        description={
                            <FormattedMessage
                                id="p_addresses_address_text"
                                values={{
                                    city: item.city,
                                    zip: item.zip,
                                    street: item.street,
                                    house: item.house,
                                    corp: item.corp,
                                    flat: item.flat,
                                }}
                            />
                        }
                        actions={
                            <>
                                <Button
                                    aria-label="Редактировать"
                                    kind="primary"
                                    outlined
                                    onClick={event => {
                                        event.stopPropagation();

                                        setShowForm({
                                            id: item.id,
                                        });
                                    }}
                                >
                                    <EditIcon size="15" className={styles.icon} />
                                </Button>
                                <Button
                                    aria-label="Удалить"
                                    kind="primary"
                                    outlined
                                    onClick={event => {
                                        event.stopPropagation();

                                        handleRemoveAddress({
                                            variables: {
                                                input: {
                                                    id: item.id,
                                                },
                                            },
                                        });
                                    }}
                                >
                                    <RemoveIcon size="15" className={styles.icon} />
                                </Button>
                            </>
                        }
                    />
                ))
            ) : (
                <p>
                    <FormattedMessage id="p_addresses_empty_text" />
                </p>
            )}
            <div>
                <Button
                    kind="primary"
                    onClick={() => {
                        setShowForm({});
                    }}
                >
                    <FormattedMessage id="p_addresses_add_address" />
                </Button>
            </div>
        </Container>
    );
};

export default Addresses;
