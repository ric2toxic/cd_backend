import React from 'react';
import { Route } from 'react-router-dom';
import profile from '../profile';
import seller_page from '../seller_agreement/seller_page';

export default [
    <Route exact path="/profile" component={profile} />,
    <Route exact path="/seller_agreement" component={seller_page} />
];
