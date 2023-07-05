import React, {Component} from 'react';
import { connect } from "react-redux";
import createCard from './conditionsOrFilters/CardCreator';
import CardHeader from './CardHeader';
import CardFields from './CardFields';
import ContextActions from './actions/ContextActions';
import ColumnActions from './actions/ColumnActions';

const actions = {
    removeContextOffer: ContextActions.removeOffer,
    removeColumnOffer: ColumnActions.removeOffer
}

class OfferCard extends Component
{
    static mapStateToProps(state, props) {
        return props;
    };

    render() 
    {
        const offerCard = createCard('offers', this.props.offerData);

        return (
            <div className="w-full min-w-65 bg-gray-100 relative rounded-3 w-full">
                <div className="relative w-full h-20 bg-gray-300 rounded-t-3 overflow-hidden">
                    <img src={offerCard.getIconURL()} className={`absolute max-w-full h-auto border-none max-w-initial ${offerCard.getIconClasses?.() ?? ''}`}/>
                </div>
                <div className="space-y-4 w-full h-full p-3">
                    <CardHeader card={offerCard} onClose={() => {
                        switch (this.props.offersSetRelation) {
                            case 'context':
                                this.props.removeContextOffer(
                                    this.props.columnTemporaryId,
                                    this.props.contextTemporaryID,
                                    offerCard.data.temporaryID
                                )
                            break;
                            case 'column':
                                this.props.removeColumnOffer(
                                    this.props.columnTemporaryId,
                                    offerCard.data.temporaryID
                                )
                            break;
                            default:
                                throw new Error('OfferCard must have a offersSetRelation')
                            break;
                        }
                    }}/>
                    <CardFields card={offerCard}/>
                </div>
            </div>
        );
    }
}

export default connect(OfferCard.mapStateToProps, actions)(OfferCard);