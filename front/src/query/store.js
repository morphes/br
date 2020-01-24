import gql from 'graphql-tag';

const GET_STORE = gql`
    query store($slug: String) {
        store(slug: $slug) {
            name
            full_name
            longitude
            latitude
        }
    }
`;

export default GET_STORE;
