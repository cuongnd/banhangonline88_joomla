package vantinviet.core.components.com_hikashop.views.checkout.tmpl;

/**
 * Created by cuongnd on 31/03/2017.
 */

/**
 * Created by pratap.kesaboyina on 24-12-2014.
 */

import android.content.Context;
import android.support.v7.widget.RecyclerView;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import java.util.ArrayList;

import vantinviet.core.R;
import vantinviet.core.libraries.joomla.JFactory;
import vantinviet.core.libraries.legacy.application.JApplication;

public class StepListDataAdapter extends RecyclerView.Adapter<StepListDataAdapter.StepRowHolder> {

    private ArrayList<String> steps;
    private Context mContext;

    public StepListDataAdapter(Context context, ArrayList<String> steps) {
        this.steps = steps;
        this.mContext = context;
    }

    @Override
    public StepRowHolder onCreateViewHolder(ViewGroup viewGroup, int i) {
        View v = LayoutInflater.from(viewGroup.getContext()).inflate(R.layout.components_com_hikashop_views_checkout_tmpl_step_item_step, null);
        StepRowHolder mh = new StepRowHolder(v);
        return mh;
    }

    @Override
    public void onBindViewHolder(StepRowHolder holder, int i) {

        String step = steps.get(i);
        if(i<this.steps.size()){
            step=step+"   > ";
        }
        holder.step.setText(step);
    }

    @Override
    public int getItemCount() {
        return (null != steps ? steps.size() : 0);
    }

    public class StepRowHolder extends RecyclerView.ViewHolder {
        protected TextView step;
        private JApplication app= JFactory.getApplication();
        public StepRowHolder(View view) {
            super(view);
            this.step = (TextView) view.findViewById(R.id.step_item);


        }

    }

}