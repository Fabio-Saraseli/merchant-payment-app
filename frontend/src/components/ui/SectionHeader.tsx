type SectionHeaderProps = {
  title: string;
  description: string;
};

function SectionHeader({ title, description }: SectionHeaderProps) {
  return (
    <div className="text-left">
      <h2 className="text-lg font-semibold text-slate-900 sm:text-xl">
        {title}
      </h2>

      <p className="mt-1 text-sm text-slate-500">{description}</p>
    </div>
  );
}

export default SectionHeader;
