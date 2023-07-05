import React, {Component} from 'react';
import { connect } from "react-redux";
import Tippy from '@tippyjs/react/headless'; 
import classNames from 'classnames';
import {offers} from './conditionsOrFilters/CardCreator';
import ContextActions from './actions/ContextActions';
import ColumnActions from './actions/ColumnActions';

const actions = {
    newContextOffer: ContextActions.newOffer,
    newColumnOffer: ColumnActions.newOffer
}

class OffersSelector extends Component
{
    state = {
        panelIsOpen: false
    };

    static mapStateToProps(state, props) {
        return props;
    };

    render() 
    {
        return (
            <Tippy
                allowHTML={true}
                followCursor={true}
                placement="auto-end"
                interactive={true}
                visible={this.state.panelIsOpen}
                onClickOutside={() => this.setState({panelIsOpen: false})}
                render={attrs => (
                <div className="w-60 z-1000 px-4 sm:px-0" {...attrs}>
                  <div className="overflow-hidden rounded-2 shadow-lg bg-gray-150">
                        <div className="flex flex-row space-x-1">
                            <div className="flex flex-col w-full">
                                <h1 className="text-2x text-gray-500 px-4 py-3">{__('Offers', CouponsPlus.textDomain)}</h1>
                                <div className="relative w-full h-full grid gap-4 bg-white p-4 grid-cols-1">
                                    {offers.map((Offer) => {
                                        const offer = new Offer({});
                                        const offerCanBeUsed = !this.props
                                                                       
                                                                       .offers
                                                                       .find(offerOrFilterData => {
                                                                            return offerOrFilterData.type === Offer.TYPE;
                                                                       });
                                        return (
                                            <button
                                            key={Offer.TYPE}
                                            disabled={offerCanBeUsed? false : true}
                                            onClick={() => {

                                                switch (this.props.offersSetRelation) {
                                                    case 'context':
                                                        this.props.newContextOffer(
                                                            this.props.columnTemporaryId,
                                                            this.props.contextTemporaryID,
                                                            Offer.TYPE
                                                        );
                                                    break;
                                                    case 'column':
                                                        this.props.newColumnOffer(
                                                            this.props.columnTemporaryId,
                                                            Offer.TYPE
                                                        );
                                                    break;
                                                    default:
                                                        throw new Error('OffersSelector must have a offersSetRelation')
                                                    break;
                                                }

                                                this.setState({
                                                    panelIsOpen: false
                                                })
                                            }}
                                            className={classNames(
                                                "flex items-center p-2 -m-3 transition duration-150 ease-in-out rounded-lg hover:bg-gray-50 focus:outline-none focus-visible:ring focus-visible:ring-orange-500 focus-visible:ring-opacity-50",
                                                {
                                                    'opacity-30': !offerCanBeUsed
                                                }
                                            )}
                                          >
                                            <div className="flex items-center justify-center flex-shrink-0 rounded-3 w-10 h-10 bg-gray-300 text-white ">
                                                {offer.getIcon()}
                                            </div>
                                            <div className="flex flex-col items-start ml-4">
                                              <p className="text-sm font-medium text-gray-900">
                                                {offer.getTitle()}
                                              </p>
                                              <p className="text-sm text-gray-500 text-left">
                                                {offer.getDescription && offer.getDescription() || ''}
                                              </p>
                                            </div>
                                          </button>
                                        )
                                    })}
                                </div>
                            </div>
                        </div>
                      <div className="p-4 bg-gray-50">
                        <a
                          href="https://couponspluspro.com/d/offers"
                          target="_blank"
                          className="flow-root px-2 py-2 transition duration-150 ease-in-out rounded-md hover:bg-gray-100 focus:outline-none focus-visible:ring focus-visible:ring-orange-500 focus-visible:ring-opacity-50"
                        >
                          <span className="flex items-center">
                            <span className="text-sm font-medium text-gray-900">
                              {__('Documentation', CouponsPlus.textDomain)}
                            </span>
                          </span>
                          <span className="block text-sm text-gray-500">
                            {__('Read the guide on using Coupons+ offers', window.CouponsPlus.textDomain)}.
                          </span>
                        </a>
                      </div>
                    </div>
                </div>
                )}
              >
              <button className="space-x-1 items-center justify-center mt-[-8px] inline-flex bg-gray-400 text-gray-100 px-8 h-8 rounded-1" onClick={() => this.setState({panelIsOpen: true})}>
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clipRule="evenodd" />
                    </svg>
                    <span>{CouponsPlus.text.add.offers}</span>
                </button>
              </Tippy>
        );
    }
}

export default connect(OffersSelector.mapStateToProps, actions)(OffersSelector);